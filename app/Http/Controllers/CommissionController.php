<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Marketer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CommissionController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('can:commissions.view')
             ->only(['index','show','invoice','clientVisits']);
        $this->middleware('can:commissions.create')
             ->only(['create','store']);
        $this->middleware('can:commissions.complete')
             ->only(['completeCommission']);
        $this->middleware('can:commissions.export')
             ->only(['export']);
        $this->middleware('can:commissions.deliver')
             ->only([
                 'deliverRequest','deliverConfirm','deliverStore',
                 'checkPromoCode','checkDeliveryCode'
             ]);
        $this->middleware('can:commissions.view')
             ->only(['details']);
    }

    public function index(Request $request)
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'العمولات', 'url' => route('commissions.index')],
        ];
        $pageTitle = 'العمولات';

        $mainTab = $request->get('mainTab', 'all');
        $subTab  = $request->get('subTab', 'all');

        $base = Commission::with(['marketer','marketingEmployee','site','creator'])
                ->where('dishes','!=',0)
                ->orderByDesc('created_at');

        if ($mainTab === 'marketer') {
            $base->whereNotNull('marketer_id');
        } elseif ($mainTab === 'employee') {
            $base->whereNotNull('employee_id');
        }

        if ($request->filled('created_at')) {
            $base->whereDate('created_at', $request->created_at);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $base->where(function ($q) use ($s) {
                $q->whereHas('site', fn($qq)=>$qq->where('name','like',"%{$s}%"))
                  ->orWhereHas('marketer', function($qq) use ($s) {
                        $qq->where('name','like',"%{$s}%")
                           ->orWhere('marketing_code','like',"%{$s}%")
                           ->orWhere('phone','like',"%{$s}%");
                   })
                  ->orWhereHas('marketingEmployee', function($qq) use ($s) {
                        $qq->where('name','like',"%{$s}%")
                           ->orWhere('marketing_code','like',"%{$s}%")
                           ->orWhere('phone','like',"%{$s}%");
                   })
                  ->orWhereHas('creator', fn($qq)=>$qq->where('username','like',"%{$s}%"));
            });
        }

        $counts = [
            'all'       => (clone $base)->whereIn('received', [0, 1])->count(),
            'delivered' => (clone $base)->where('received', 1)->count(),
            'pending'   => (clone $base)->where('received', 0)->count(),
            'marketer'  => (clone $base)->whereNotNull('marketer_id')->count(),
            'employee'  => (clone $base)->whereNotNull('employee_id')->count(),
        ];

        $query = clone $base;
        if ($subTab === 'delivered')  $query->where('received', 1);
        if ($subTab === 'pending')    $query->where('received', 0);

        $perPage = $request->get('per_page', 10);

        $commissions = $query->paginate($perPage)->withQueryString();

        return view('commissions.index', compact(
            'commissions','breadcrumbs','pageTitle','mainTab','subTab','counts'
        ));
    }

    public function show($id)
    {
        $commission = Commission::with(['marketer', 'marketingEmployee', 'site', 'creator'])->findOrFail($id);

        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'العمولات', 'url' => route('commissions.index')],
            ['label' => 'عرض العمولة', 'url' => '']
        ];

        $pageTitle = 'عرض العمولة';

        return view('commissions.show', [
            'commission' => $commission,
            'breadcrumbs' => $breadcrumbs,
            'pageTitle' => $pageTitle
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Commission::with(['site','marketer','marketingEmployee','creator']);

        if ($s = $request->string('search')->trim()) {
            $query->where(function ($q) use ($s) {
                $q->whereHas('marketer', fn($qq) => $qq->where('name','like',"%$s%")
                                                      ->orWhere('phone','like',"%$s%")
                                                      ->orWhere('marketing_code','like',"%$s%"))
                  ->orWhereHas('marketingEmployee', fn($qq) => $qq->where('name','like',"%$s%")
                                                                 ->orWhere('phone','like',"%$s%")
                                                                 ->orWhere('marketing_code','like',"%$s%"));
            });
        }

        if ($date = $request->date('created_at')) {
            $query->whereDate('created_at', $date);
        }

        $type = $request->get('type', 'all');
        if ($type === 'marketer')   $query->whereNotNull('marketer_id');
        if ($type === 'employee')   $query->whereNotNull('employee_id');

        $subTab = $request->get('subTab', 'all');
        if ($subTab === 'delivered') $query->where('received', 1);
        if ($subTab === 'pending')   $query->where('received', 0);

        $rows = $query->orderBy('id')->get();

        $dynamicCols = [
            'index'             => 'م',
            'site'              => 'الموقع',
            'person'            => 'المسوّق / الموظف',
            'marketing_code'    => 'الرقم التسويقي',
            'visitors'          => 'عدد الزوار',
            'dishes'            => 'عدد الأطباق',
            'commission_amount' => 'مبلغ العمولة',
            'invoice_amount'    => 'مبلغ الفاتورة',
        ];

        if ($type === 'all') {
            $dynamicCols['visit_type'] = 'نوع الزيارة';
        }
        if ($subTab === 'all') {
            $dynamicCols['status'] = 'الحالة';
        }
        $dynamicCols['creator']    = 'اسم المستخدم';
        $dynamicCols['created_at'] = 'تاريخ الإضافة';

        $total_commission = $rows->sum('commission_amount');
        $total_invoice    = $rows->sum('invoice_amount');
        $total_visitors   = $rows->sum('visitors');
        $total_dishes     = $rows->sum('dishes');

        $format   = $request->get('format', 'xls');
        $fileName = 'commissions_' . now()->format('Ymd_His') . '.' . ($format === 'csv' ? 'csv' : 'xls');

        if ($format === 'csv') {
            $headers = [
                "Content-Type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename={$fileName}",
            ];

            $callback = function () use ($rows, $dynamicCols, $type, $subTab, $total_commission, $total_invoice, $total_visitors, $total_dishes) {
                $out = fopen('php://output', 'w');
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM

                fputcsv($out, array_values($dynamicCols));

                $i = 0;
                foreach ($rows as $r) {
                    $i++;
                    $personName  = $r->marketer->name ?? $r->marketingEmployee->name ?? '—';
                    $personPhone = $r->marketer->phone ?? $r->marketingEmployee->phone ?? '—';
                    $personCode  = $r->marketer->marketing_code ?? $r->marketingEmployee->marketing_code ?? '—';

                    $line = [
                        $i,
                        $r->site->name ?? '—',
                        trim($personName.' ('.$personPhone.')'),
                        $personCode,
                        $r->visitors,
                        $r->dishes,
                        $r->commission_amount,
                        $r->invoice_amount,
                    ];

                    if ($type === 'all') $line[] = $r->marketer_id ? 'مسوّق' : 'موظف';
                    if ($subTab === 'all') $line[] = $r->received ? 'تم التسليم' : 'لم يتم التسليم';

                    $line[] = $r->creator->name ?? '—';
                    $line[] = optional($r->created_at)->format('Y-m-d');

                    fputcsv($out, $line);
                }

                $totals = ['الإجمالي', '', '', '', $total_visitors, $total_dishes, $total_commission, $total_invoice];
                if ($type === 'all') $totals[] = '';
                if ($subTab === 'all') $totals[] = '';
                $totals[] = '';
                $totals[] = '';
                fputcsv($out, $totals);

                fclose($out);
            };

            return response()->streamDownload($callback, $fileName, $headers);
        }

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ];

        $today = now()->translatedFormat('l');
        $date  = now()->translatedFormat('d F Y');

        $callback = function () use ($rows, $dynamicCols, $type, $subTab, $total_commission, $total_invoice, $total_visitors, $total_dishes, $today, $date) {
            echo '<html><head><meta charset="UTF-8"></head><body dir="rtl">';
            echo '<table width="100%" style="margin-bottom:20px;font-family:Tahoma,Arial,sans-serif;font-size:13pt;font-weight:bold;">';
            echo '<tr>';
            echo '<td style="text-align:center;vertical-align:middle;" colspan="3">';
            echo '<img src="'.asset('assets/images/new-logos/logo-sm.png').'" alt="logo" style="height:48px;">';
            echo '</td>';
            echo '</tr>';
            echo '<tr><td colspan="3" style="height:18px;"></td></tr>';
            echo '<tr><td colspan="3" style="height:18px;"></td></tr>';
            echo '<tr><td colspan="3" style="height:18px;"></td></tr>';
            echo '<tr>';
            echo '<td style="text-align:right;vertical-align:middle;">اليوم: '.$today.'</td>';
            echo '<td></td>';
            echo '<td style="text-align:left;vertical-align:middle;">التاريخ: '.$date.'</td>';
            echo '</tr>';
            echo '</table>';

            echo '<div style="height:40px;"></div>';

            echo '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-family:Tahoma,Arial,sans-serif;font-size:12pt;">';
            echo '<tr><td colspan="'.(count($dynamicCols)+1).'" style="text-align:center;font-weight:bold;background:#e6f2ff;font-size:16pt;">جدول عمولات السائقين</td></tr>';

            echo '<tr>';
            foreach ($dynamicCols as $key => $label) {
                if ($key === 'person') {
                    echo '<th style="background:#d9ead3;font-weight:bold;text-align:center;">'.$label.'</th>';
                    echo '<th style="background:#d9ead3;font-weight:bold;text-align:center;">رقم الهاتف</th>';
                } else {
                    echo '<th style="background:#d9ead3;font-weight:bold;text-align:center;">'.$label.'</th>';
                }
            }
            echo '</tr>';

            $i = 0;
            foreach ($rows as $r) {
                $i++;
                echo '<tr>';
                echo '<td>'.$i.'</td>';
                echo '<td>'.e($r->site->name ?? '—').'</td>';

                $personName  = $r->marketer->name ?? $r->marketingEmployee->name ?? '—';
                $personPhone = $r->marketer->phone ?? $r->marketingEmployee->phone ?? '—';
                if (is_numeric($personPhone) && strpos($personPhone, '+') === 0) {
                    $personPhone = '="' . $personPhone . '"';
                } elseif (is_numeric($personPhone)) {
                    $personPhone = '="' . $personPhone . '"';
                }
                echo '<td>'.e($personName).'</td>';
                echo '<td>'.e($personPhone).'</td>';

                $personCode  = $r->marketer->marketing_code ?? $r->marketingEmployee->marketing_code ?? '—';
                echo '<td>'.e($personCode).'</td>';

                echo '<td style="text-align:center;">'.(int)$r->visitors.'</td>';
                echo '<td style="text-align:center;">'.(int)$r->dishes.'</td>';
                echo '<td style="mso-number-format:\'#\\,##0.00\';">'.(float)$r->commission_amount.'</td>';
                echo '<td style="mso-number-format:\'#\\,##0.00\';">'.(float)$r->invoice_amount.'</td>';

                if ($type === 'all') echo '<td>'.($r->marketer_id ? 'مسوّق' : 'موظف').'</td>';
                if ($subTab === 'all') echo '<td>'.($r->received ? 'تم التسليم' : 'لم يتم التسليم').'</td>';

                echo '<td>'.e($r->creator->name ?? '—').'</td>';
                echo '<td>';
                if ($r->created_at) {
                    $date = $r->created_at->format('Y-m-d');
                    $time = $r->created_at->format('h:i');
                    $ampm = $r->created_at->format('A') === 'AM' ? 'صباحاً' : 'مساءً';
                    echo e($date) . '<br><span style="font-size:11pt;color:#888;">' . e($time) . ' ' . $ampm . '</span>';
                } else {
                    echo '—';
                }
                echo '</td>';
                echo '</tr>';
            }

            echo '<tr style="background:#fff2cc;font-weight:bold;">';
            $prefixCols = 5;
            echo str_repeat('<td></td>', $prefixCols);
            echo '<td style="text-align:center;">'.$total_visitors.'</td>';
            echo '<td style="text-align:center;">'.$total_dishes.'</td>';
            echo '<td>'.$total_commission.'</td>';
            echo '<td>'.$total_invoice.'</td>';
            $remaining = (count($dynamicCols)+1) - ($prefixCols + 4);
            echo str_repeat('<td></td>', max(0,$remaining));
            echo '</tr>';

            echo '</table></body></html>';
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function deliverRequest($id)
    {
        $commission = Commission::findOrFail($id);

        // Using a short code for simplicity and user convenience
        $code = rand(100, 999);

        $commission->update([
            'delivery_code' => $code,
        ]);
        $phone = $commission->marketer?->phone ?? $commission->employee?->phone;
        if ($phone) {
            $msg = "كود تسليم العمولة الخاصة بك هو: {$code}";
            $instance_id = 'instance136478';
            $token = 'xyn17h5jc304w5pr';
            $url = "https://api.ultramsg.com/$instance_id/messages/chat";

            \Illuminate\Support\Facades\Http::post($url, [
                'token' => $token,
                'to' => $phone,
                'body' => $msg,
            ]);
        }

        return redirect()->route('commissions.deliverConfirm', $commission->id);
    }

    public function checkPromoCode(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|max:255',
            'commission_id' => 'required|integer|exists:commissions,id',
        ]);

        $code = $request->input('promo_code');
        $commissionId = $request->input('commission_id');

        $commission = Commission::find($commissionId);

        $valid = false;

        if ($commission) {
            if ($commission->marketer_id) {
                $marketer = \App\Models\Marketer::where('id', $commission->marketer_id)
                    ->where('marketing_code', $code)
                    ->first();
                if ($marketer) {
                    $valid = true;
                }
            }
            if (!$valid && $commission->employee_id) {
                $employee = \App\Models\User::where('id', $commission->employee_id)
                    ->where('marketing_code', $code)
                    ->first();
                if ($employee) {
                    $valid = true;
                }
            }
        }

        return response()->json([
            'valid' => $valid,
        ]);
    }

    public function checkDeliveryCode(Request $request)
    {
        $request->validate([
            'delivery_code' => 'required|string',
            'commission_id' => 'required|integer|exists:commissions,id',
        ]);

        $commission = Commission::find($request->commission_id);

        $valid = $commission && $commission->delivery_code === $request->delivery_code;

        return response()->json(['valid' => $valid]);
    }

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

        if ($personType === 'marketer') {
            $data = $request->validate([
                'marketer_code'    => ['required','exists:marketers,marketing_code'],
                'visitors_count'   => ['required','integer','min:0'],
                'attach' => ['required','file','mimes:jpg,jpeg,png,gif,svg,bmp,webp,heic,heif','max:30720'],
            ]);

            $marketer = Marketer::where('marketing_code', $data['marketer_code'])->first();

            if ($request->hasFile('attach')) {
                $file = $request->file('attach');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $destination = storage_path('app/public/visits');
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }
                $file->move($destination, $filename);
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

        // Notify marketer to collect commission after completion
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
            'delivery_method'   => 'required|in:code,promo',
            'delivered_amount'  => 'required|numeric|min:0',
            'delivery_code'     => 'required_if:delivery_method,code|string|nullable',
            'promo_code'        => 'required_if:delivery_method,promo|string|nullable',
        ]);

        if ($request->delivery_method === 'code') {
            if ($commission->delivery_code !== $request->delivery_code) {
                return back()->withErrors(['delivery_code' => 'الكود غير صحيح لهذه العمولة.']);
            }

            $commission->update([
                'received' => 1,
                'commission_amount' => $request->delivered_amount,
                'delivery_code' => null,
                'promo_code' => null,
                'promo_image' => null,
            ]);
        } elseif ($request->delivery_method === 'promo') {
            $valid = false;
            if ($commission->marketer_id) {
                $marketer = \App\Models\Marketer::where('id', $commission->marketer_id)
                    ->where('marketing_code', $request->promo_code)
                    ->first();
                if ($marketer) $valid = true;
            }
            if (!$valid && $commission->employee_id) {
                $employee = \App\Models\User::where('id', $commission->employee_id)
                    ->where('marketing_code', $request->promo_code)
                    ->first();
                if ($employee) $valid = true;
            }
            if (!$valid) {
                return back()->withErrors(['promo_code' => 'الرمز غير صحيح أو لا يخص هذه العمولة.']);
            }

            $promoImagePath = null;
            if ($request->hasFile('promo_image')) {
                $promoImagePath = $request->file('promo_image')->store('promo_signatures', 'public');
            }

            $commission->update([
                'received' => 1,
                'commission_amount' => $request->delivered_amount,
                'delivery_code' => null,
                'promo_code' => $request->promo_code,
                'promo_image' => $promoImagePath,
            ]);
        }

        return redirect()->route('commissions.index')->with('success', 'تم تأكيد تسليم العمولة بنجاح.');
    }

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
