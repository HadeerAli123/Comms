<div class="my-4 page-header-breadcrumb d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1 class="page-title fw-medium fs-18 mb-2" style="display:none;">
            {{ $pageTitle ?? ((isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0) ? ($breadcrumbs[count($breadcrumbs)-1]['label'] ?? '') : '') }}
        </h1>
@if(isset($breadcrumbs) && is_array($breadcrumbs))
  <ol class="breadcrumb mb-0 d-flex align-items-center flex-wrap" style="direction: rtl;">
    @foreach($breadcrumbs as $index => $breadcrumb)
        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" {!! $loop->last ? 'aria-current=page' : '' !!}>
            @if(isset($breadcrumb['url']) && !$loop->last)
                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
            @else
                {{ $breadcrumb['label'] }}
            @endif
        </li>
    @endforeach
</ol>
@endif
    </div>
    <div>
        <div style="display: none;">
            <button class="btn btn-primary-light btn-wave me-2">
            <i class="bx bx-crown align-middle"></i> Plan Upgrade
            </button>
            <button class="btn btn-secondary-light btn-wave me-0">
            <i class="ri-upload-cloud-line align-middle"></i> Export Report
            </button>
        </div>
    </div>
</div>

{{-- Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        @php $success = session('success'); @endphp
        @if(is_array($success))
            @if(isset($success['title']))
                <strong>{{ $success['title'] }}</strong><br><br>
            @endif
            @if(isset($success['message']))
                {{ $success['message'] }}
            @endif
        @else
            {{ $success }}
        @endif
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        @php $error = session('error'); @endphp
        @if(is_array($error))
            @if(isset($error['title']))
                <strong>{{ $error['title'] }}</strong><br>
            @endif
            @if(isset($error['message']))
                {{ $error['message'] }}
            @endif
        @else
            {{ $error }}
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
