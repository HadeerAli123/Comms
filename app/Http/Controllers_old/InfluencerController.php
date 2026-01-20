<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use App\Models\User;
use App\Models\Commission;
use App\Models\Client;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use App\Models\Visit;
use App\Models\InfluencerOperation;

class InfluencerController extends Controller
{
    /**
     * Display a listing of the influencers.
     */
   public function index(Request $request)
{
    $breadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('dashboard')],
        ['label' => 'مشاهير المسوقين', 'url' => route('influencers.index')],
    ];

  $query = Influencer::with(['employee', 'country', 'latestOperation'])
    ->withMax('operations', 'created_at')
    // إجمالي كل الزيارات
    ->withCount('visits')
    // إجمالي الزيارات المُعلَنَة فقط (اختياري لو تحب تعرضه)
    ->withCount(['visits as announced_visits_count' => function ($q) {
        $q->where('is_announced', true);
    }])
    // متوسط تقييم الزيارات المُعلَنَة فقط
    ->withAvg(['visits as announced_avg_rating' => function ($q) {
        $q->where('is_announced', true);
    }], 'rating');


    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    if ($request->filled('employee_id')) {
        $query->where('employee_id', $request->employee_id);
    }

    $query->orderByDesc('operations_max_created_at')
          ->orderByDesc('id');

    $influencers = $query->paginate(10)->withQueryString();

    $employees = User::pluck('name', 'id');
    $pageTitle = 'قائمة مشاهير الاعلانات';
    $countries = Country::all();

    // Visits tab counts
    $visits_all = Visit::query();
    $visits_done = Visit::where('is_announced', true);
    $visits_pending = Visit::where('is_announced', false);

    if ($request->filled('search')) {
        $search = $request->search;
        $visits_all->whereHas('influencer', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
        $visits_done->whereHas('influencer', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
        $visits_pending->whereHas('influencer', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }
    if ($request->filled('employee_id')) {
        $employee_id = $request->employee_id;
        $visits_all->whereHas('influencer', function ($q) use ($employee_id) {
            $q->where('employee_id', $employee_id);
        });
        $visits_done->whereHas('influencer', function ($q) use ($employee_id) {
            $q->where('employee_id', $employee_id);
        });
        $visits_pending->whereHas('influencer', function ($q) use ($employee_id) {
            $q->where('employee_id', $employee_id);
        });
    }

    return view('influencers.index', [
        'influencers' => $influencers,
        'breadcrumbs' => $breadcrumbs,
        'pageTitle' => $pageTitle,
        'employees' => $employees,
        'countries' => $countries,
        'visits_all' => $visits_all->get(),
        'visits_done' => $visits_done->get(),
        'visits_pending' => $visits_pending->get(),
    ]);

}

/**
 * Show the form for creating a new influencer.
 */
public function create()
    {
        $sites = Site::whereNull('parent_id')->pluck('name', 'id');
        $employees = User::pluck('name', 'id');
        return view('influencers.create', compact('sites', 'employees'));
    }


    /**
     * Store a newly created influencer.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'exists:users,id'],
            'ads_link' => ['nullable', 'url', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'whatsapp_link' => ['nullable', 'url', 'max:255'],
            'instagram_link' => ['nullable', 'url', 'max:255'],
            'tiktok_link' => ['nullable', 'url', 'max:255'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'snap' => ['nullable', 'string', 'max:255'],
            'snap_link' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:20480'], // 20MB max
        ]);

        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('influencers', 'public');
        }

        $influencer = Influencer::create([
            'name' => $data['name'],
            'ads_link' => $data['ads_link'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'whatsapp_link' => $data['whatsapp_link'] ?? null,
            'instagram_link' => $data['instagram_link'] ?? null,
            'tiktok_link' => $data['tiktok_link'] ?? null,
            'employee_id' => $data['employee_id'],
            'balance' => $data['balance'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'snap' => $data['snap'] ?? null,
            'snap_link' => $data['snap_link'] ?? null,
            'phone' => $data['phone'] ?? null,
            'pdf' => $pdfPath,
            'basic_balance' => $data['balance'] ?? 0,
        ]);

        return redirect()->route('influencers.index')->with('success', 'تم إضافة المشهور بنجاح');
    }

    public function sendWhatsAppUltraMsg($to, $message)
    {
        $instance_id = 'instance134276';
        $token = '5yoe7ysf1oz0cen9';

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
     * Display the specified influencer.
     */
    public function show(Influencer $influencer)
    {
        $breadcrumbs = [
            ['name' => 'الرئيسية', 'url' => route('dashboard')],
            ['name' => 'المؤثرون', 'url' => route('influencers.index')],
            ['name' => $influencer->name, 'url' => '#'],
        ];
        return view('influencers.show', compact('influencer', 'breadcrumbs'));
    }

    /**
     * Show the form for editing the specified influencer.
     */
    public function edit(Influencer $influencer)
    {
        $sites = Site::whereNull('parent_id')->pluck('name', 'id');
        return view('influencers.edit', compact('influencer', 'sites'));
    }

    /**
     * Update the specified influencer.
     */
    public function update(Request $request, Influencer $influencer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'exists:users,id'],
            'ads_link' => ['nullable', 'url', 'max:255'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'whatsapp_link' => ['nullable', 'url', 'max:255'],
            'instagram_link' => ['nullable', 'url', 'max:255'],
            'tiktok_link' => ['nullable', 'url', 'max:255'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'snap' => ['nullable', 'string', 'max:255'],
            'snap_link' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:20480'], // 20MB max
        ]);

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('influencers', 'public');
            $data['pdf'] = $pdfPath;
        }

        $influencer->update([
            'name' => $data['name'],
            'ads_link' => $data['ads_link'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'whatsapp_link' => $data['whatsapp_link'] ?? null,
            'instagram_link' => $data['instagram_link'] ?? null,
            'tiktok_link' => $data['tiktok_link'] ?? null,
            'employee_id' => $data['employee_id'],
            'balance' => $data['balance'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'snap' => $data['snap'] ?? null,
            'snap_link' => $data['snap_link'] ?? null,
            'phone' => $data['phone'] ?? null,
            'pdf' => $data['pdf'] ?? $influencer->pdf,
        ]);

        return redirect()->route('influencers.index')->with('success', 'تم التعديل بنجاح');
    }


    /**
     * Remove the specified influencer.
     */
    public function destroy(Influencer $influencer)
    {
        $influencer->delete();
        return back()->with('success', 'تم الحذف بنجاح.');
    }

  public function addVisit(Request $request, Influencer $influencer)
{
    $data = $request->validate([
        'amount'        => 'required|numeric|min:0.01',
        'notes'         => 'nullable|string|max:1000',
        'people_count'  => 'nullable|integer|min:0',
    ]);

    DB::transaction(function () use ($data, $influencer) {
        $locked = \App\Models\Influencer::whereKey($influencer->id)->lockForUpdate()->first();

        Visit::create([
            'influencer_id' => $locked->id,
            'amount'        => $data['amount'],
            'notes'         => $data['notes'] ?? null,
            'people_count'  => $data['people_count'] ?? 0,
            'user_id'       => auth()->id(),
            'is_announced'  => false,
        ]);

        InfluencerOperation::create([
            'influencer_id' => $locked->id,
            'operation_type'=> 'visit',          // ['recharge','visit']
            'amount'        => $data['amount'],
            'notes'         => $data['notes'] ?? null,
            'employee_id'   => auth()->id(),
        ]);

        $locked->decrement('balance', $data['amount']);
    });

    return back()->with('success', 'تم إضافة الزيارة وخصم المبلغ بنجاح');
}

public function chargeBalance(Request $request, Influencer $influencer)
{
    $data = $request->validate([
        'charge_amount' => 'required|numeric|min:0.01',
        'notes'         => 'nullable|string|max:1000',
    ]);

    DB::transaction(function () use ($data, $influencer) {
        $locked = \App\Models\Influencer::whereKey($influencer->id)->lockForUpdate()->first();

        InfluencerOperation::create([
            'influencer_id' => $locked->id,
            'operation_type'=> 'recharge',
            'amount'        => $data['charge_amount'],
            'notes'         => $data['notes'] ?? null,
            'employee_id'   => auth()->id(),
        ]);

        $locked->increment('balance', $data['charge_amount']);
    });

    return back()->with('success', 'تم شحن الرصيد بنجاح');
}
}
