<?php
namespace App\Http\Controllers;

use App\Models\Marketer;
use App\Models\User;
use App\Models\Commission;
use App\Models\Client;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
class MarketerController extends BaseController
{
        use AuthorizesRequests, ValidatesRequests;
public function __construct()
{
    // CRUD المسوّقين
    $this->middleware('permission:marketers.view')->only(['index','show']);
    $this->middleware('permission:marketers.create')->only(['create','store']);
    $this->middleware('permission:marketers.edit')->only(['edit','update']);
    $this->middleware('permission:marketers.delete')->only(['destroy']);

    // استلام/تسليم عمولة (اعتماد التسليم)
    $this->middleware('permission:commissions.approve_delivery')->only([
        'receiveCommissionForm','received'
    ]);
}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Marketer::with([
            'site',
            'employee',
            'commissions' => function ($query) {
                $query->where('received', 0);
            }
        ])->withSum(['commissions as commissions_sum' => function ($query) {
            $query->where('received', 0);
        }], 'commission_amount');

        // Handle search by name or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('marketing_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('site_id') && !empty($request->site_id)) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->has('employee_id') && !empty($request->employee_id)) {
            $query->where('employee_id', $request->employee_id);
        }

$marketers = $query->paginate(10)->withQueryString();

$sites = Site::whereNull('parent_id')->pluck('name', 'id');
$employees = User::pluck('name', 'id');

$pageTitle = 'قائمة المسوّقين';
$breadcrumbs = [
    ['label' => 'الرئيسية', 'url' => route('dashboard')],
    ['label' => 'المسوّقون', 'url' => route('marketers.index')],
];

// Add site breadcrumb if site_id is present
if ($request->has('site_id') && !empty($request->site_id)) {
    $siteName = $sites[$request->site_id] ?? '';
    $breadcrumbs[] = [
        'label' => $siteName,
        'url' => '#'
    ];
}

// Add subsite breadcrumb if subsite_id is present
if ($request->has('subsite_id') && !empty($request->subsite_id)) {
    $subsite = Site::find($request->subsite_id);
    if ($subsite) {
        $breadcrumbs[] = [
            'label' => $subsite->name,
            'url' => '#'
        ];
    }
}

        return view('marketers.index', [
            'marketers' => $marketers,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => $pageTitle,
            'sites' => $sites,
            'employees' => $employees
        ]);
    }

    /**
     * Show the form to receive commissions for a marketer.
     */
    public function receiveCommissionForm(Marketer $marketer)
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'المسوّقون', 'url' => route('marketers.index')],
            ['label' => $marketer->name, 'url' => route('marketers.show', $marketer)],
            ['label' => 'استلام عمولة', 'url' => '#'],
        ];

 $marketer_first = Marketer::where('id',$marketer->id)->with([
            'site',
            'employee',
            'commissions'
        ])->withSum('commissions as commissions_sum', 'commission_amount')->first();
//dd($marketer_first->commissions_sum);
        return view('marketers.received', [
            'marketer' => $marketer,
            'commissions_sum' => $marketer_first->commissions_sum,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => 'استلام عمولة'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sites = Site::whereNull('parent_id')->pluck('name', 'id');

        $employees = User::pluck('name', 'id');
        return view('marketers.create', compact('sites', 'employees'));
    }
    /**
     * Mark a marketer as having received their commission.
     */
    public function received(Request $request, Marketer $marketer)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $amount = $request->input('amount');
        $remaining = $amount;

        // Get marketer's unpaid commissions (received = 0), ordered oldest first
        $commissions = Commission::where('marketer_id', $marketer->id)
            ->where('received', 0)
            ->orderBy('id')
            ->get();

        foreach ($commissions as $commission) {
            if ($remaining <= 0) break;

            if ($commission->commission_amount <= $remaining) {
                // Mark commission as received
                $commission->received = 1;
                $commission->save();
                $remaining -= $commission->commission_amount;
            } else {
                // Partial payment, update commission_amount and mark as received
                $commission->commission_amount -= $remaining;
                $commission->save();
                $remaining = 0;
            }
        }

        // Update marketer's commission_balance
        $commission_balance = Commission::where('marketer_id', $marketer->id)
            ->where('received', 0)
            ->sum('commission_amount');
        $commission_balance_value = max($commission_balance, 0);
        $marketer->save();

        // Log payout in balance table as debit
        \DB::table('balance')->insert([
            'amount'      => $amount,
            'type'        => 'debit',
            'description' => 'استلام عمولة مسوق: ' . $marketer->name,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('marketers.index', $marketer)
                         ->with('success', 'تم تسجيل استلام العمولة بنجاح.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:6'],
            'phone' => ['required', 'string', 'max:20', 'unique:marketers,phone'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'employee_id' => ['required', 'exists:users,id'],
            'branch_id' => ['nullable', 'exists:sites,id'],
        ]);

        // Combine country code and phone, remove leading zeros from phone
        $cleanPhone = ltrim($data['phone'], '0');
        $fullPhone = $data['country_code'] . $cleanPhone;

        // Get the last marketer with a 4-digit marketing_code >= 1000
        $lastMarketer = Marketer::where('marketing_code', '>=', 1000)
            ->orderByDesc('marketing_code')
            ->first();

        $code = $lastMarketer ? $lastMarketer->marketing_code + 1 : 1000;

        // Ensure the code is unique in both marketers and users tables
        while (
            Marketer::where('marketing_code', $code)->exists() ||
            User::where('marketing_code', $code)->exists()
        ) {
            $code++;
        }

        $marketer = Marketer::create([
            'name' => $data['name'],
            'phone' => $fullPhone,
            'site_id' => $data['site_id'] ?? null,
            'employee_id' => $data['employee_id'],
            'branch_id' => $data['branch_id'] ?? null,
            'marketing_code' => $code,
            'commission_balance' => 0,
        ]);
 $welcomeMessage = "أهلا بك عزيزي المسوق في مطعم الكوت، نرحب بتعاونك ونسعى للنجاح سوياً\nرقمك التسويقي هو {$marketer->marketing_code}\nفي حال وجود أي اختلاف أو مشكلة يرجى التواصل معنا على الرقم 01019011249\nنتمنى لك تجربة رائعة معنا ونجاح مستمر، فريق مطعم الكوت دائماً في خدمتك.";
        $res = $this->sendWhatsAppUltraMsg($fullPhone, $welcomeMessage);
//dd($res);

        return redirect()->route('marketers.index')->with('success', [
            'title' => 'تم إضافة المسوّق بنجاح',
            'message' => "أهلا بك عزيزي المسوق في مطعم الكوت، نرحب بتعاونك ونسعى للنجاح سوياً\nرقمك التسويقي هو {$marketer->marketing_code}"
        ]);
    }


public function sendWhatsAppUltraMsg($to, $message)
{
    $instance_id = 'instance136478';
    $token = 'xyn17h5jc304w5pr';

    $url = "https://api.ultramsg.com/$instance_id/messages/chat";

    $response = Http::post($url, [
        'token' => $token,
        'to' => $to,
        'body' => $message,
    ]);

    if ($response->successful()) {
        \Log::info('تم إرسال الرسالة بنجاح');
        return $response->json();
    } else {
        \Log::error('فشل الإرسال', ['response' => $response->body()]);
        return [
            'success' => false,
            'error' => $response->body()
        ];
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Marketer $marketer)
    {
        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('dashboard')],
            ['name' => 'المسوّقون', 'url' => route('marketers.index')],
            ['name' => $marketer->name, 'url' => '#'],
        ];
        return view('marketers.show', compact('marketer', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marketer $marketer)
    {
        $sites = Site::whereNull('parent_id')->pluck('name', 'id');
        return view('marketers.edit', compact('marketer', 'sites'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marketer $marketer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:6'],
            'phone' => ['required', 'string', 'max:20'],
            'site_id' => ['nullable', 'exists:sites,id'],
        ]);

        // Combine country code and phone, remove leading zeros from phone
        $cleanPhone = ltrim($data['phone'], '0');
        $fullPhone = $data['country_code'] . $cleanPhone;

        $marketer->update([
            'name' => $data['name'],
            'phone' => $fullPhone,
            'site_id' => $data['site_id'] ?? null,
        ]);

        return redirect()->route('marketers.index')
                         ->with('success', 'تم تعديل المسوّق بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marketer $marketer)
    {
        $marketer->delete();
        return back()->with('success', 'تم حذف المسوّق.');
    }
}
