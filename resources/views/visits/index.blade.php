@extends('layout.app')

@section('title', 'الزيارات')

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        @include('partials.crumb')

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            زيارات المشاهير
                        </div>



                        <div class="row w-100">
                     
<ul class="nav nav-pills mb-4 commission-tabs" id="visits-tabs" role="tablist">

    <li class="nav-item">
        <button class="nav-link active"
                id="all-tab"
                data-bs-toggle="pill"
                data-bs-target="#all"
                type="button">
            الكل ({{ $visits_all->count() }})
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                id="announced-tab"
                data-bs-toggle="pill"
                data-bs-target="#done"
                type="button">
            تم الإعلان ({{ $visits_done->count() }})
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                id="not-announced-tab"
                data-bs-toggle="pill"
                data-bs-target="#pending"
                type="button">
            لم يتم الإعلان ({{ $visits_pending->count() }})
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link"
                id="pending-tab"
                data-bs-toggle="pill"
                data-bs-target="#not-specified"
                type="button">
            لم يتم التحديد ({{ $visits_not_specified->count() }})
        </button>
    </li>

</ul>
   </div></div>
   <div class="card-body">
                        <div class="tab-content mt-3" id="visits-tabs-content">
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                @include('visits.partials.table', ['visits' => $visits_all])
                            </div>
                            <div class="tab-pane fade" id="done" role="tabpanel" aria-labelledby="done-tab">
                                @include('visits.partials.table', ['visits' => $visits_done])
                            </div>
                            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                @include('visits.partials.table', ['visits' => $visits_pending])
                            </div>

                            <div class="tab-pane fade" id="not-specified" role="tabpanel" aria-labelledby="not-specified-tab">
                                @include('visits.partials.table', ['visits' => $visits_not_specified])
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
