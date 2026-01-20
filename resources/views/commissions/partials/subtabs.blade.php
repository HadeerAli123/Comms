@php
    // متوقعين المتغيرات: $type ('all' أو 'marketer' أو 'employee'), $subTab ('all'|'delivered'|'pending'), $counts = ['all'=>..,'delivered'=>..,'pending'=>..]
    $makeUrl = function (string $wanted) use ($type) {
        // نحافظ على بقية البارامترات، ونغير subTab + mainTab
        return request()->fullUrlWithQuery([
            'mainTab' => $type,
            'subTab'  => $wanted,
            'page'    => 1, // نرجّع لأولى صفحة عند التبديل
        ]);
    };
@endphp
<!-- Tabs الفرعية -->
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


{{-- مافيش تبويب محتوى ديناميكي هنا — الجدول تحت بيستخدم $commissions اللي الكنترولر فلترها --}}
@include('commissions.partials.table', ['commissions' => $commissions])
