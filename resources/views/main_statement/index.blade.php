@extends('layout.app')

@section('title', 'كشف الحساب الرئيسي')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">

        @include('partials.crumb')

        <!-- Start:: row-1 -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card p-4 border-0">
                    <div class="commission-section">
                        <div class="page-title mb-4">
                            <h2>كشف الحساب الرئيسي</h2>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div class="d-flex gap-2 align-items-center">

                            </div>
                            <div class="d-flex gap-2">
                                @can('main-statement.add-capital')
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addCapitalModal">
                                    + إضافة رأس مال
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <!-- Cards Section -->
                    <div class="cards my-3">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="statement-card">
                                    <i class="fas fa-coins"></i>
                                    <h5 class="title-card">إجمالي الدائن</h5>
                                    <h6>{{ number_format($total_income, 2) }}</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="statement-card">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <h5 class="title-card">إجمالي المدين</h5>
                                    <h6>{{ number_format($total_expense, 2) }}</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="statement-card">
                                    <i class="fas fa-wallet"></i>
                                    <h5 class="title-card">الرصيد الحالي</h5>
                                    <h6>{{ number_format($balance, 2) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('main-statement.add-capital')
                    <div class="modal fade" id="addCapitalModal" tabindex="-1" aria-labelledby="addCapitalModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form method="POST" action="{{ route('main-statement.addCapital') }}"  >
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCapitalModalLabel">إضافة رأس مال</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="capitalAmount" class="form-label fw-bold">المبلغ</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="capitalAmount" name="amount" placeholder="أدخل المبلغ" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="capitalDescription" class="form-label fw-bold">البيان (اختياري)</label>
                                            <input type="text" class="form-control" id="capitalDescription" name="description">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger">حفظ</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endcan

                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead>
                                <tr class="table-danger">
                                    <th>م</th>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                    <th>دائن</th>
                                    <th>مدين</th>
                                    <th>الرصيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $index => $txn)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('Y-m-d h:i A') }}</td>
                                    <td>{{ $txn->description ?? ($txn->type === 'credit' ? 'دخل' : 'مصروف') }}</td>
                                    <td>{{ number_format($txn->type === 'credit' ? $txn->amount : 0, 2) }}</td>
                                    <td>{{ number_format($txn->type === 'debit' ? $txn->amount : 0, 2) }}</td>
                                    <td>{{ number_format($txn->running_balance, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>

    </div>
</div>
@endsection
