@extends('layout.app')

@section('title', 'المسوقين')
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
@section('content')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            @include('partials.crumb')


            <!-- Start:: row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                تسليم العمولة
                            </div>

                        </div>


                        <div class="card-body">
                            <form action="{{ route('marketers.received', $marketer->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="received-amount-{{ $marketer->id }}" class="form-label fw-bold">
                                        مبلغ التسليم
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="uil uil-money-bill"></i></span>
                                        <input
                                            id="received-amount-{{ $marketer->id }}"
                                            type="number"
                                            name="amount"
                                            min="0"
                                            max="{{ $commissions_sum }}"
                                            class="form-control"
                                            required
                                            placeholder="أدخل المبلغ"
                                        >
                                    </div>
                                </div>
                                <div class="mb-3 text-end">
                                    <span class="text-muted">إجمالي الرصيد الحالي :</span>
                                    <strong class="text-success">{{ number_format($commissions_sum,2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success px-4">
                                        <i class="bi bi-check-circle me-2"></i> تسليم
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <!-- End:: row-1 -->



        </div>
    </div>
    <!-- End::app-content -->

@endsection
