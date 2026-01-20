<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MarketerController;
use App\Http\Controllers\MarketingEmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfluencerController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    // Fetch counts and totals for dashboard
    $mainSitesCount   = \App\Models\Site::whereNull('parent_id')->count();
    $marketersCount   = \App\Models\Marketer::count();
    $totalCommissions = \App\Models\Commission::sum('commission_amount');

    return view('dashboard', compact('mainSitesCount', 'marketersCount', 'totalCommissions'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    /** ======================= مواقع التسويق (Sites) ======================= */
    Route::get('/sites/{site}/subsites', [SiteController::class, 'subsites'])
        ->middleware('permission:sites.view')->name('sites.subsites.index');

    Route::resource('sites', SiteController::class)->middleware([
        'index'   => 'permission:sites.view',
        'show'    => 'permission:sites.view',

    ]);

    /** ======================= المسوقون (Marketers) ======================= */
    Route::resource('marketers', MarketerController::class)->middleware([
        'index'   => 'permission:marketers.view',
        'show'    => 'permission:marketers.view',

    ]);

    // شاشات الاستلام الخاصة بالمسوقين (تحتاج موافقة/تسليم)
    Route::get('/marketers/{marketer}/receive-commission', [MarketerController::class, 'receiveCommissionForm'])
        ->middleware('permission:commissions.approve_delivery')->name('marketers.receiveCommissionForm');
    Route::post('/marketers/{marketer}/received', [MarketerController::class, 'received'])
        ->middleware('permission:commissions.approve_delivery')->name('marketers.received');

    // تفاصيل المسوق
    Route::get('/marketers/details/{code}', [CommissionController::class, 'details'])
        ->middleware('permission:marketers.view')->name('marketers.details');

    /** ======================= مشاهير الإعلانات (Influencers) ======================= */
    Route::resource('influencers', InfluencerController::class)->middleware([
        'index'   => 'permission:influencers.view',
        'show'    => 'permission:influencers.view',

    ]);
    Route::post('/influencers/{influencer}/visit', [InfluencerController::class, 'addVisit'])
        ->middleware('permission:influencers.add_visit')->name('influencers.addVisit');
    Route::post('/influencers/{influencer}/charge-balance', [InfluencerController::class, 'chargeBalance'])
        ->middleware('permission:influencers.recharge_balance')->name('influencers.chargeBalance');

    /** ======================= زيارات العملاء (Visits) ======================= */
    Route::get('/visits', [VisitController::class, 'index'])
        ->middleware('permission:visits.view')->name('visits.index');
    Route::patch('/visits/{visit}/toggle-status', [VisitController::class, 'toggleStatus'])
        ->middleware('permission:visits.edit')->name('visits.toggleStatus');

    /** ======================= العمولات (Commissions) ======================= */
    Route::get('commissions/client_visits', [CommissionController::class, 'clientVisits'])
        ->middleware('permission:commissions.view')->name('commissions.client_visits');

    Route::get('/commissions/export', [CommissionController::class, 'export'])
        ->middleware('permission:reports.export')->name('commissions.export'); // أو اعملي permission: commissions.export لو حبيتي

    Route::resource('commissions', CommissionController::class)->middleware([
        'index'   => 'permission:commissions.view',
        'show'    => 'permission:commissions.view',

    ]);

    Route::post('/commissions/{commission}/complete', [CommissionController::class, 'completeCommission'])
        ->middleware('permission:commissions.approve_delivery')->name('commissions.complete');

    Route::get('commissions/{commission}/invoice', [CommissionController::class, 'invoice'])
        ->middleware('permission:commissions.view')->name('commissions.invoice');

    // تدفقات التسليم بالكود
    Route::post('/commissions/{id}/deliver-request', [CommissionController::class, 'deliverRequest'])
        ->middleware('permission:commissions.approve_delivery')->name('commissions.deliverRequest');
    Route::get('/commissions/{id}/deliver-confirm', [CommissionController::class, 'deliverConfirm'])
        ->middleware('permission:commissions.approve_delivery')->name('commissions.deliverConfirm');
    Route::post('/commissions/{id}/deliver-store', [CommissionController::class, 'deliverStore'])
        ->middleware('permission:commissions.approve_delivery')->name('commissions.deliverStore');

    // فحوصات الأكواد
    Route::post('/commissions/check-promo-code', [CommissionController::class, 'checkPromoCode'])
        ->middleware('permission:commissions.view')->name('commissions.checkPromoCode');
    Route::post('/commissions/check-delivery-code', [CommissionController::class, 'checkDeliveryCode'])
        ->middleware('permission:commissions.view')->name('commissions.checkDeliveryCode');

    /** ======================= موظفو التسويق (Marketing Employees) ======================= */
    // (كان فيه تكرار للـ resource — خليه مرّة واحدة)
    Route::resource('marketing-employees', MarketingEmployeeController::class)->middleware([
        'index'   => 'permission:employees.view',
        'show'    => 'permission:employees.view',
      
    ]);
    Route::get('/api/employees/{employee}', [MarketingEmployeeController::class, 'show'])
        ->middleware('permission:employees.view')->name('api.employees.show');
    Route::get('/users/details/{code}', [MarketingEmployeeController::class, 'details'])
        ->middleware('permission:employees.view')->name('users.details');

    /** ======================= كشف الحساب الرئيسي (Main Statement / Ledger) ======================= */
    Route::get('main-statement', [App\Http\Controllers\MainStatementController::class, 'index'])
        ->middleware('permission:ledger.view')->name('main-statement.index');
    Route::post('main-statement/add-capital', [App\Http\Controllers\MainStatementController::class, 'addCapital'])
        ->middleware('permission:ledger.add_capital')->name('main-statement.addCapital');

    /** ======================= الفروع (Branches) ======================= */
    Route::get('branches', [BranchController::class, 'index'])
        ->middleware('permission:sites.view')->name('branches.index');

    // API فروع الموقع
    Route::get('/api/sites/{site}/branches', function ($siteId) {
        $site     = \App\Models\Site::find($siteId);
        $subsites = $site ? $site->children()->get(['id','name']) : collect();
        return response()->json($subsites);
    })->middleware('permission:sites.view')->name('api.sites.branches');

    /** ======================= العملاء (Clients) ======================= */
    Route::get('clients', [ClientController::class, 'index'])
        ->middleware('permission:reports.view')->name('clients.index'); // أو اعملي clients.view لو عايزة تفصّليها

    /** ======================= الدول (Countries) ======================= */
    // لو ما عندكيش permissions مخصوصة ليها، خليه للأدمن فقط:
    Route::resource('countries', CountryController::class)->middleware('role:Admin');

    /** ======================= الأدوار والصلاحيات (Admin only) ======================= */
    Route::resource('roles', RoleController::class)->middleware('role:Admin');
    Route::resource('permissions', PermissionController::class)->middleware('role:Admin');

    /** ======================= البروفايل ======================= */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';