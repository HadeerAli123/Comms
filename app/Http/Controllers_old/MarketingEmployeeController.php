<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarketingEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['label' => 'الرئيسية', 'url' => route('dashboard')],
            ['label' => 'موظفين التسويق', 'url' => route('marketing-employees.index')],
        ];

        $pageTitle = 'موظفين التسويق';

        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $employees = $query
            ->withCount('marketers')
            ->withSum('commissions', 'commission_amount')
            ->with('roles')
            ->paginate(10)
            ->withQueryString();
 $roles = \Spatie\Permission\Models\Role::all();
        return view('marketing-employees.index', [
            'employees' => $employees,
            'breadcrumbs' => $breadcrumbs,
            'roles' => $roles,
            'pageTitle' => $pageTitle
        ]);
    }
    public function show($id)
    {
        $employee = User::findOrFail($id);

        return response()->json([
            'id'    => $employee->id,
            'name'  => $employee->name,
            'phone' => $employee->phone,
            'username' => $employee->username,
            'email' => $employee->email,
        ]);
    }
    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('marketing-employees.create', compact('roles'));
    }

    public function details($code)
    {
        $employee = User::where('marketing_code', $code)->first();

        if ($employee) {
            return response()->json([
                'name'           => $employee->name,
                'phone'          => $employee->phone,
                'marketing_code' => $employee->marketing_code,
                'username'       => $employee->username,
                'email'          => $employee->email,
            ]);
        }

        return response()->json(null, 404);
    }

    public function edit(User $marketing_employee)
    {
         $roles = \Spatie\Permission\Models\Role::all();
        return view('marketing-employees.edit', compact('marketing_employee','roles'));
    }

public function store(Request $request)
{
    $data = $request->validate([
        'username' => ['required','string','max:50','unique:users,username'],
        'name'     => ['required','string','max:255'],
        'email'    => ['required','email','unique:users,email'],
        'phone'    => ['required', 'string', 'max:20'],
        'password' => ['required','string','min:8','confirmed'],
        'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
                    'country_code' => ['required', 'string', 'max:6'],

    ], [
        'percentage.required' => 'النسبة مطلوبة.',
        'percentage.numeric'  => 'النسبة يجب أن تكون رقمية.',
        'percentage.min'      => 'النسبة يجب ألا تقل عن 0.',
        'percentage.max'      => 'النسبة يجب ألا تزيد عن 100.',
        'username.required' => 'اسم المستخدم مطلوب.',
        'username.string'   => 'اسم المستخدم يجب أن يكون نصاً.',
        'username.max'      => 'اسم المستخدم يجب ألا يزيد عن 50 حرفاً.',
        'username.unique'   => 'اسم المستخدم مستخدم من قبل.',
        'name.required'     => 'الاسم مطلوب.',
        'name.string'       => 'الاسم يجب أن يكون نصاً.',
        'name.max'          => 'الاسم يجب ألا يزيد عن 255 حرفاً.',
        'email.required'    => 'البريد الإلكتروني مطلوب.',
        'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة.',
        'email.unique'      => 'البريد الإلكتروني مستخدم من قبل.',
        'password.required' => 'كلمة المرور مطلوبة.',
        'password.string'   => 'كلمة المرور يجب أن تكون نصاً.',
        'password.min'      => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
        'password.confirmed'=> 'تأكيد كلمة المرور غير مطابق.',
    ]);

            // Combine country code and phone, remove leading zeros from phone
        $cleanPhone = ltrim($data['phone'], '0');
        $fullPhone = $data['country_code'] . $cleanPhone;


    $lastUser = User::where('marketing_code', '>=', 1000)
        ->orderByDesc('marketing_code')
        ->first();

    $marketingCode = $lastUser ? $lastUser->marketing_code + 1 : 1000;

    while (
        User::where('marketing_code', $marketingCode)->exists() ||
        \App\Models\Marketer::where('marketing_code', $marketingCode)->exists()
    ) {
        $marketingCode++;
    }

    $user = User::create([
        'username'        => $data['username'],
        'name'            => $data['name'],
        'email'           => $data['email'],
        'phone'           => $fullPhone,
        'prec'            => $data['percentage'],
        'password'        => bcrypt($data['password']),
        'marketing_code'  => $marketingCode,
    ]);
 $user->syncRoles($request->roles ?? []); // إضافة المجموعات

    if (!empty($user->phone)) {
        $fullPhone = $user->phone;
        $this->sendWhatsAppUltraMsg($fullPhone, "أهلاً بك لدينا بمطعم الكوت ورقمك التسويقي: {$user->marketing_code}");
    }

    return redirect()->route('marketing-employees.index')->with('success', [
        'title' => 'تم إضافة موظف التسويق بنجاح',
        'message' => "أهلا بك عزيزي الموظف في مطعم الكوت، نرحب بتعاونك ونسعى للنجاح سوياً\nرقمك التسويقي هو {$user->marketing_code}"
    ]);
}


public function update(Request $request, User $marketing_employee)
{
    $data = $request->validate([
        'username' => ['required','string','max:50','unique:users,username,'.$marketing_employee->id],
        'name'     => ['required','string','max:255'],
        'email'    => ['required','email','email,'.$marketing_employee->id],
        'password' => ['nullable','string','min:8','confirmed'],
        'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
    ], [
        'percentage.required' => 'النسبة مطلوبة.',
        'percentage.numeric'  => 'النسبة يجب أن تكون رقمية.',
        'percentage.min'      => 'النسبة يجب ألا تقل عن 0.',
        'percentage.max'      => 'النسبة يجب ألا تزيد عن 100.',
        'username.required' => 'اسم المستخدم مطلوب.',
        'username.string'   => 'اسم المستخدم يجب أن يكون نصاً.',
        'username.max'      => 'اسم المستخدم يجب ألا يزيد عن 50 حرفاً.',
        'username.unique'   => 'اسم المستخدم مستخدم من قبل.',
        'name.required'     => 'الاسم مطلوب.',
        'name.string'       => 'الاسم يجب أن يكون نصاً.',
        'name.max'          => 'الاسم يجب ألا يزيد عن 255 حرفاً.',
        'email.required'    => 'البريد الإلكتروني مطلوب.',
        'email.email'       => 'صيغة البريد الإلكتروني غير صحيحة.',
        'password.required' => 'كلمة المرور مطلوبة.',
        'password.string'   => 'كلمة المرور يجب أن تكون نصاً.',
        'password.min'      => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
        'password.confirmed'=> 'تأكيد كلمة المرور غير مطابق.',
    ]);

    $update = [
        'username' => $data['username'],
        'name'     => $data['name'],
        'email'    => $data['email'],
         'prec'    => $data['percentage'],
    ];

    if (! empty($data['password'])) {
        $update['password'] = bcrypt($data['password']);
    }

    $marketing_employee->update($update);
    $marketing_employee->syncRoles($request->roles ?? []);
    return redirect()->route('marketing-employees.index')
                     ->with('success','تم تحديث بيانات الموظف بنجاح.');
}

    public function destroy(User $marketing_employee)
    {
        $marketing_employee->delete();
        return back()->with('success','تم حذف الموظف بنجاح.');
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

}