<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
class MainStatementController extends BaseController

{
    use AuthorizesRequests, ValidatesRequests;
public function __construct()
    {
 $this->middleware('permission:main-statement.view')->only(['index']);
 $this->middleware('permission:main-statement.add-capital')->only(['addCapital']);
    }
public function index()
{
    // ترتيب من الأقدم للأحدث لحساب الرصيد بشكل صحيح
    $transactions = \App\Models\Balance::orderBy('created_at', 'asc')->get();

    $total_income = $transactions->where('type', 'credit')->sum('amount');
    $total_expense = $transactions->where('type', 'debit')->sum('amount');
    $balance = $total_income - $total_expense;

    // حساب الرصيد لكل صف
    $running_balance = 0;
    foreach ($transactions as $transaction) {
        if ($transaction->type === 'credit') {
            $running_balance += $transaction->amount;
        } elseif ($transaction->type === 'debit') {
            $running_balance -= $transaction->amount;
        }
        $transaction->running_balance = $running_balance;
    }

    // إعادة الترتيب تنازلي للعرض
    $transactions = $transactions->sortByDesc('created_at');

    $breadcrumbs = [
        ['label' => 'الرئيسية', 'url' => route('dashboard')],
        ['label' => 'كشف الحساب الرئيسي', 'url' => route('main-statement.index')],
    ];

    return view('main_statement.index', compact(
        'transactions',
        'total_income',
        'total_expense',
        'balance',
        'breadcrumbs'
    ));
}
public function addCapital(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
    ]);

    \App\Models\Balance::create([
        'amount' => $request->amount,
        'type' => 'credit',
        'description' => 'إضافة رأس المال: ' . $request->description,
    ]);

    return redirect()->route('main-statement.index')->with('success', 'تم إضافة رأس المال بنجاح.');
}
}