@php
    $makeUrl = function (string $wanted) use ($type) {
        // Reset to first page when switching tabs to avoid pagination issues
        return request()->fullUrlWithQuery([
            'mainTab' => $type,
            'subTab'  => $wanted,
            'page'    => 1,
        ]);
    };
@endphp
<ul class="nav nav-pills mb-3 commission-tabs" role="tablist">

    <li class="nav-item" role="presentation">
        <a href="{{ $makeUrl('all') }}"
           class="nav-link {{ $subTab === 'all' ? 'active' : '' }}">
            الكل
            @if(isset($counts['all']) || isset($commissions))
                ({{ $counts['all'] ?? $commissions->total() }})
            @endif
        </a>
    </li>

    <li class="nav-item" role="presentation">
        <a href="{{ $makeUrl('delivered') }}"
           class="nav-link {{ $subTab === 'delivered' ? 'active' : '' }}">
            تم التسليم
            @if(isset($counts['delivered']))
                ({{ $counts['delivered'] }})
            @endif
        </a>
    </li>

    <li class="nav-item" role="presentation">
        <a href="{{ $makeUrl('pending') }}"
           class="nav-link {{ $subTab === 'pending' ? 'active' : '' }}">
            لم يتم التسليم
            @if(isset($counts['pending']))
                ({{ $counts['pending'] }})
            @endif
        </a>
    </li>

</ul>

@include('commissions.partials.table', ['commissions' => $commissions])
