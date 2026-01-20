<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Marketer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class CommissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
{
    $breadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('dashboard')],
        ['label' => 'العمولات', 'url' => route('commissions.index')],
    ];

    $pageTitle = 'العمولات';

    // التاب الرئيسي (all, marketer, employee)
    $mainTab = $request->get('mainTab', 'all');
    // التاب الفرعي (all, delivered, pending)
    $subTab = $request->get('subTab', 'all');

    $query = Commission::with(['marketer', 'marketingEmployee', 'site', 'creator'])
        ->where('dishes', '!=', 0)
        ->orderByDesc('created_at');

    // ✅ فلترة حسب التاب الرئيسي
    if ($mainTab === 'marketer') {
        $query->whereNotNull('marketer_id');
    } elseif ($mainTab === 'employee') {
        $query->whereNotNull('employee_id');
    }

    // ✅ فلترة حسب التاب الفرعي
    if ($subTab === 'delivered') {
        $query->where('received', 1);
    } elseif ($subTab === 'pending') {
        $query->where('received', 0);
    }

    // ✅ فلتر التاريخ
    if ($request->filled('created_at')) {
        $query->whereDate('created_at', $request->created_at);
    }

    // ✅ فلتر البحث
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('site', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('marketer', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('marketing_code', 'like', "%{$search}%");
            })
            ->orWhereHas('employee', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('creator', function ($q2) use ($search) {
                $q2->where('username', 'like', "%{$search}%");
            });
        });
    }

    $commissions = $query->paginate(10)->withQueryString();

    return view('commissions.index', [
        'commissions' => $commissions,
        'breadcrumbs' => $breadcrumbs,
        'pageTitle' => $pageTitle,
        'mainTab' => $mainTab,
        'subTab' => $subTab,
    ]);
}
public function deliverRequest($id)
{
    $commission = Commission::findOrFail($id);

    // توليد كود عشوائي من 3 أرقام
    $code = rand(100, 999);

    // حفظه في العمولة
    $commission->update([
        'delivery_code' => $code,
    ]);
    // إرسال رسالة واتساب (مثال باستخدام ultramsg)
    $phone = $commission->marketer?->phone ?? $commission->employee?->phone;
    if ($phone) {
        $msg = "كود تسليم العمولة الخاصة بك هو: {$code}";
        $instance_id = 'instance134276';
        $token = '5yoe7ysf1oz0cen9';
        $url = "https://api.ultramsg.com/$instance_id/messages/chat";

        \Illuminate\Support\Facades\Http::post($url, [
            'token' => $token,
            'to' => $phone,
            'body' => $msg,
        ]);
    }

    // توجيه لصفحة إدخال الكود والمبلغ
    return redirect()->route('commissions.deliverConfirm', $commission->id);
}
    /**
     * Display a listing of the client visits.
     */
    public function clientVisits(Request $request)
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'زيارات العملاء', 'url' => route('commissions.client_visits')],
        ];

        $pageTitle = 'زيارات العملاء';

        $query = Commission::with(['marketer', 'site', 'creator'])
            ->whereNull('commission_amount')
            ->where('dishes',0)
            ->where('received', 0)
            ->orderByDesc('created_at');

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
            $q->whereHas('site', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('marketer', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('marketing_code', 'like', "%{$search}%");
            })
            ->orWhereHas('creator', function ($q2) use ($search) {
                $q2->where('username', 'like', "%{$search}%");
            });
            });
        }

        $clientVisits = $query->paginate(10)->withQueryString();

        return view('commissions.client_visits', [
            'commissions' => $clientVisits,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => $pageTitle
        ]);
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $breadcrumb = [
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Commissions', 'url' => route('commissions.index')],
            ['label' => 'Create', 'url' => '']
        ];
        return view('commissions.create');
    }


public function details($code)
{
    $marketer = Marketer::where('marketing_code', $code)
        ->with([ 'employee', 'site'])
        ->first();
    if ($marketer) {
        return response()->json([
            'name'      => $marketer->name,
            'phone'     => $marketer->phone,
            'marketing_code'=> $marketer->marketing_code,
            'employee'  =>  $marketer->employee ? $marketer->employee->username : null,
            'site'      => $marketer->site ? $marketer->site->name : null,
        ]);
    }

    return response()->json(null, 404);
}
public function store(Request $request)
{
    $personType = $request->input('person_type');
//dd($request->all());
    if ($personType === 'marketer') {
        $data = $request->validate([
            'marketer_code'    => ['required','exists:marketers,marketing_code'],
            'visitors_count'   => ['required','integer','min:0'],
'attach' => ['required','file','mimes:jpg,jpeg,png,gif,svg,bmp,webp,heic,heif'
,'max:30720'],
      ]);

        $marketer = Marketer::where('marketing_code', $data['marketer_code'])->first();

if ($request->hasFile('attach')) {
    $file = $request->file('attach');

    // اسم فريد للملف
    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

    // مكان التخزين (داخل storage/app/public/visits)
    $destination = storage_path('app/public/visits');

    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }

    // نقل الملف
    $file->move($destination, $filename);

    // مسار التخزين بالنسبة للـ DB
    $attachPath = "visits/{$filename}";
} else {
    return back()->withErrors(['attach' => 'فشل رفع الملف']);
}
        $commission = Commission::create([
            'marketer_id'      => $marketer->id,
            'site_id'          => $marketer->site_id,
            'visitors'         => $data['visitors_count'],
            'type'             => 'marketer',
            'created_by'       => auth()->id(),
            'updated_by'       => auth()->id(),
            'attach'           => $attachPath,
        ]);

        return redirect()
            ->route('commissions.client_visits')
            ->with('success', 'تم حفظ الزيارة بنجاح.')
            ->with('commission_id', $commission->id);

    } elseif ($personType === 'employee') {
        $data = $request->validate([
            'employee_code'    => ['required','exists:users,marketing_code'],
            'visitors_count_employee'   => ['required','integer','min:0'],
            'cheque_amount'    => ['required','numeric','min:0'],
            'invoice_amount'   => ['required','numeric','min:0'],
            'commission_amount' => ['nullable','numeric','min:0'],
        'invoice_number'    => ['nullable','string','max:255'],
            'dishes_count'     => ['required','integer','min:0'],
            'discount_rate'    => ['nullable','numeric','min:0','max:100'],
        ]);

        $employee = \App\Models\User::where('marketing_code', $data['employee_code'])->first();

$commission = Commission::create([
    'employee_id'      => $employee->id,
    'visitors'         => $data['visitors_count_employee'],
    'cheque_amount'    => $data['cheque_amount'],
    'invoice_amount'   => $data['invoice_amount'],
    'commission_amount' => $data['commission_amount'] ?? null,
    'invoice_number'    => $data['invoice_number'] ?? null,
    'dishes'           => $data['dishes_count'],
    'discount_rate'    => $data['discount_rate'] ?? null,
    'type'             => 'employee',
    'site_id'         => 1,
    'created_by'       => auth()->id(),
    'updated_by'       => auth()->id(),
]);

return redirect()
    ->route('commissions.client_visits')
    ->with('success', 'تم حفظ الزيارة للموظف بنجاح.')
    ->with('commission_id', $commission->id);

    } else {
        return back()->withErrors(['person_type' => 'يرجى اختيار نوع الشخص.']);
    }
}

/**
 * استكمال بيانات العمولة بعد الزيارة
 */
public function completeCommission(Request $request, $id)
{
    $data = $request->validate([
        'dishes_count'      => ['nullable','integer','min:0'],
        'commission_amount' => ['nullable','numeric','min:0'],
        'invoice_amount'    => ['nullable','numeric','min:0'],
        'invoice_number'    => ['nullable','string','max:255'],
        'invoice_image'     => ['nullable','image','mimes:jpeg,png,jpg,gif,svg,webp,bmp,tiff','max:2048'],
    ]);

    $commission = Commission::findOrFail($id);

    // Handle invoice image upload
    $invoiceImagePath = $commission->invoice_image;
    if ($request->hasFile('invoice_image')) {
        $invoiceImagePath = $request->file('invoice_image')->store('invoices', 'public');
    }

    $commission->update([
        'dishes'           => $data['dishes_count'] ?? null,
        'commission_amount'=> $data['commission_amount'] ?? null,
        'invoice_amount'   => $data['invoice_amount'] ?? null,
        'invoice_number'   => $data['invoice_number'] ?? null,
        'invoice_image'    => $invoiceImagePath,
        'updated_by'       => auth()->id(),
    ]);

    // Send WhatsApp message to marketer after commission is completed
    $marketer = $commission->marketer;
    $fullPhone = $marketer->phone;
    $msg = "برجاء استلام العمولة للكود {$marketer->marketing_code}";
    $this->sendWhatsAppUltraMsg($fullPhone, $msg);

    return redirect()
        ->route('commissions.client_visits')
        ->with('success', 'تم استكمال بيانات العمولة بنجاح وتحويلها الي قسم عمولات المسوقين');
}
public function invoice($id)
{
    $commission = Commission::with(['marketer', 'site', 'creator'])->findOrFail($id);

    $breadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('dashboard')],
        ['label' => 'العمولات', 'url' => route('commissions.index')],
        ['label' => 'فاتورة العمولة', 'url' => '']
    ];

    $pageTitle = 'فاتورة العمولة';

    return view('commissions.invoice', [
        'commission' => $commission,
        'breadcrumbs' => $breadcrumbs,
        'pageTitle' => $pageTitle
    ]);
}
/**
 * Send WhatsApp message using UltraMsg API.
 */
public function sendWhatsAppUltraMsg($to, $message)
{
    $instance_id = 'instance134276';
    $token = '5yoe7ysf1oz0cen9';

    $url = "https://api.ultramsg.com/$instance_id/messages/chat";

    $response = \Illuminate\Support\Facades\Http::post($url, [
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

public function deliverStore(Request $request, $id)
{
    $commission = Commission::findOrFail($id);

    $request->validate([
        'delivery_code' => 'required|string',
        'delivered_amount' => 'required|numeric|min:0',
    ]);

    // check الكود
    if ($commission->delivery_code !== $request->delivery_code) {
        return back()->withErrors(['delivery_code' => 'الكود غير صحيح لهذه العمولة.']);
    }

    // تحديث الحالة
    $commission->update([
        'received' => 1,
        'commission_amount' => $request->delivered_amount,
        'delivery_code' => null, // نمسح الكود بعد الاستخدام
    ]);

    return redirect()->route('commissions.index')->with('success', 'تم تأكيد تسليم العمولة بنجاح.');
}

/**
 * Show the form for confirming commission delivery.
 */
public function deliverConfirm($id)
{
    $commission = Commission::findOrFail($id);

    $breadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('dashboard')],
        ['label' => 'العمولات', 'url' => route('commissions.index')],
        ['label' => 'تأكيد تسليم العمولة', 'url' => '']
    ];

    $pageTitle = 'تأكيد تسليم العمولة';

    return view('commissions.received', [
        'commission' => $commission,
        'breadcrumbs' => $breadcrumbs,
        'pageTitle' => $pageTitle
    ]);
}

}
