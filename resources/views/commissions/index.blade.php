@extends('layout.app')

@section('title', 'العمولات')

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            @include('partials.crumb')
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header">
              <div class="card border-0">
    <div class="card-body">

      <div class="card p-4 border-0">
    <div class="commission-section">

        <!-- العنوان -->
        <div class="page-title mb-4">
            <h2>العمولات</h2>
        </div>

        <!-- أدوات البحث والتصدير -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">

            <!-- البحث + التاريخ -->
            <form method="GET"
                  action="{{ route('commissions.index') }}"
                  class="d-flex gap-4 align-items-center ">

                <!-- البحث -->
                <div class="search-box position-relative">
                    <input type="text"
                           name="search"
                           class="form-control ps-5 w-100 w-md-50"
                           placeholder="بحث بالاسم أو رقم الهاتف أو الرقم التسويقي"
                           value="{{ request('search') }}"
                           aria-label="بحث" >
                    <i class="fas fa-search position-absolute"
                       style="left:15px;top:50%;transform:translateY(-50%);color:#888;"></i>
                </div>

                <!-- التاريخ -->
                <input type="date"
                       name="created_at"
                       class="form-control"
                       style="max-width:200px;"
                       value="{{ request('created_at') }}">

                <!-- زر البحث -->
                <button type="submit" class="btn btn-danger">
                    بحث
                </button>
            </form>

            <!-- التصدير -->
            @php
                $q = request()->all();
            @endphp

            @can('commissions.export')
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-danger"
                       href="{{ route('commissions.export',
                       array_merge($q, [
                           'format' => 'xls',
                           'type'   => $type ?? 'all',
                           'subTab' => $subTab ?? 'all'
                       ])) }}">
                        تصدير Excel (مُلَوَّن)
                    </a>

                    <a class="btn btn-danger"
                       href="{{ route('commissions.export',
                       array_merge($q, [
                           'format' => 'csv',
                           'type'   => $type ?? 'all',
                           'subTab' => $subTab ?? 'all'
                       ])) }}">
                        تصدير CSV
                    </a>
                </div>
            @endcan

        </div>

    </div>
</div>

                            <div class="card-body">

             <!-- Tabs الرئيسية -->
<ul class="nav nav-pills mb-3 commission-tabs" id="mainTabs" role="tablist">

    <li class="nav-item" role="presentation">
        <button class="nav-link active"
                id="all-tab"
                data-bs-toggle="pill"
                data-bs-target="#allTab"
                type="button"
                role="tab"
                aria-controls="allTab"
                aria-selected="true">
            الكل ({{ $counts['all'] ?? 0 }})
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="marketers-tab"
                data-bs-toggle="pill"
                data-bs-target="#marketersTab"
                type="button"
                role="tab"
                aria-controls="marketersTab"
                aria-selected="false">
            عمولات مسوقين
            ({{ $counts['marketer'] ?? ($counts['all'] - ($counts['employee'] ?? 0)) }})
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="employees-tab"
                data-bs-toggle="pill"
                data-bs-target="#employeesTab"
                type="button"
                role="tab"
                aria-controls="employeesTab"
                aria-selected="false">
            عمولات موظفين ({{ $counts['employee'] ?? 0 }})
        </button>
    </li>

</ul>

                                <div class="tab-content" id="mainTabsContent">

                                    <!-- Tab الكل -->
                                    <div class="tab-pane fade show active" id="allTab" role="tabpanel">
                                        @include('commissions.partials.subtabs', [
                                            'type' => 'all',
                                            'commissions' => $commissions,
                                        ])
                                    </div>

                                    <!-- Tab المسوقين -->
                                    <div class="tab-pane fade" id="marketersTab" role="tabpanel">
                                        @include('commissions.partials.subtabs', [
                                            'type' => 'marketer',
                                            'commissions' => $commissions->whereNotNull('marketer_id'),
                                        ])
                                    </div>

                                    <!-- Tab الموظفين -->
                                    <div class="tab-pane fade" id="employeesTab" role="tabpanel">
                                        @include('commissions.partials.subtabs', [
                                            'type' => 'employee',
                                            'commissions' => $commissions->whereNotNull('employee_id'),
                                        ])
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
