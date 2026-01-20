@extends('layout.app')

@section('title', 'عمولة المسوقين')

@section('content')
    <!-- Start::app-content -->
    <style>
.breadcrumb .breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb .breadcrumb-item + .breadcrumb-item::before {
    content: "»";
    padding: 0 0.5rem;
    color: #999;
    display: inline-block;
}
</style>
    <div class="main-content app-content">
        <div class="container-fluid">

            @include('partials.crumb')


          <!-- Start::row-1 -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-header d-md-flex d-block">
                                <div class="card-title">تفاصيل الفاتورة</div>
                                <div class="ms-auto mt-md-0 mt-2">
                                    <button class="btn btn-sm btn-secondary me-1 d-print-none" onclick="javascript:window.print();">طباعة<i class="ri-printer-line ms-1 align-middle d-inline-block"></i></button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="p-3 bg-light border rounded mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-xl-8">
                                            <div class="mb-2">
                                                <img src="{{ asset('assets/images/new-logos/logo.png') }}" alt="" style="width: 150px;height: 80px;">
                                            </div>
                                            <span class="badge bg-secondary-transparent mb-2">بيانات المسوق</span>
                                            <p class="mb-1 fw-semibold text-default">{{ $commission->marketer->name ?? '-' }}</p>
                                            <p class="text-muted mb-1">{{ $commission->marketer->address ?? '-' }}</p>
                                            <p class="text-muted mb-1">{{ $commission->marketer->phone ?? '-' }}</p>
                                            <p class="text-muted mb-0">{{ $commission->marketer->email ?? '-' }}</p>
                                        </div>
                                        <div class="col-xl-4">
                                           <div class="row align-items-center mb-3">
                                            <div class="col-xl-4">
                                                <label class="form-label mb-0 ">رقم الفاتورة :</label>
                                            </div>
                                            <div class="col-xl-8">
                                                #{{ $commission->id }}
                                            </div>
                                           </div>
                                           <div class="row align-items-center mb-3">
                                            <div class="col-xl-4">
                                                <label class="form-label mb-0 ">عنوان الفاتورة :</label>
                                            </div>
                                            <div class="col-xl-8">
                                                فاتورة عمولة تسويق
                                            </div>
                                           </div>
                                           <div class="row align-items-center">
                                            <div class="col-xl-4">
                                                <label class="form-label mb-0 ">المبلغ المستحق :</label>
                                            </div>
                                            <div class="col-xl-8">
                                                {{ number_format($commission->commission_amount, 2) }} {{ $commission->currency ?? 'جنيه' }}
                                            </div>
                                           </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                                                <p class="text-muted mb-2">
                                                    تفاصيل العمولة :
                                                </p>
                                                <p class="mb-1 text-muted">
                                                    الموقع: {{ $commission->site->name ?? '-' }}
                                                </p>
                                                <p class="mb-1 text-muted">
                                                    قيمة العمولة: {{ number_format($commission->commission_amount, 2) }} {{ $commission->currency ?? 'جنيه' }}
                                                </p>
                                                <p class="mb-1 text-muted">
                                                    عدد الزوار: {{ $commission->visitors ?? '-' }}
                                                </p>
                                                <p class="mb-1 text-muted">
                                                    عدد الأطباق: {{ $commission->dishes ?? '-' }}
                                                </p>
                                                <p class="mb-1 text-muted">
                                                    تاريخ الإنشاء: {{ $commission->created_at->format('Y-m-d') }}
                                                </p>

                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 ms-auto mt-sm-0 mt-3">
                                                <p class="text-muted mb-2">
                                                    بيانات المنشئ :
                                                </p>
                                                <p class="fw-bold mb-1">
                                                    {{ $commission->creator->name ?? '-' }}
                                                </p>
                                                <p class="text-muted mb-1">
                                                    {{ $commission->creator->email ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
<div class="col-xl-12 mt-4">

                                            <label for="invoice-signature" class="form-label">توقيع المسوق:</label>
                                            <div class="border rounded p-3" style="min-height:60px;">
                                                @if(!empty($commission->marketer->signature))
                                                    <img src="{{ asset('storage/' . $commission->marketer->signature) }}" alt="توقيع" height="40">
                                                @else
                                                    <span class="text-muted">.......................</span>
                                                @endif
                                            </div>

                                </div>
                                    </div>






                            </div>
                            <div class="card-footer text-end" style="display:none;">
                                <button class="btn btn-success">تحميل <i class="ri-download-2-line ms-1 align-middle"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3" style="display:none;">
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">
                                    Invoice Payment
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <p class="fs-14 fw-medium">
                                        UPI Payment
                                        </p>
                                        <p>
                                            <span class="fw-medium text-muted fs-12">Name On Card :</span> Jack Miller
                                        </p>
                                        <p>
                                            <span class="fw-medium text-muted fs-12">Card Number :</span> 1234 5678 9087 XXXX
                                        </p>
                                        <p>
                                            <span class="fw-medium text-muted fs-12">Total Amount :</span> <span class="text-success fw-medium fs-14">$2570.42</span>
                                        </p>
                                        <p>
                                            <span class="fw-medium text-muted fs-12">Issue Date :</span> 12,Oct 2024
                                        </p>
                                        <p>
                                            <span class="fw-medium text-muted fs-12">Due Date :</span> 29,Dec 2024 - <span class="text-danger fs-12 fw-medium">30 days due</span>
                                        </p>
                                        <p>
                                            <span class="fw-medium text-muted fs-12">Invoice Status : <span class="badge bg-warning-transparent">Pending</span></span>
                                        </p>
                                        <div class="alert alert-warning" role="alert">
                                            Please Make sure to pay the invoice bill within 30 days.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End::row-1 -->



        </div>
    </div>
    <!-- End::app-content -->

@endsection
