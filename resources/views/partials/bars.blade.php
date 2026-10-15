{{-- Grafik batang CSS murni. Butuh: $series = [['label','full','total','orders'], ...] --}}
@php
    $max = max(1, collect($series)->max('total'));
    $showValues = count($series) <= 8;
@endphp
<div class="bars">
    @foreach($series as $s)
        <div class="bar-col" title="{{ $s['full'] }}: {{ \App\Support\Fmt::rp($s['total']) }} ({{ $s['orders'] }} order)">
            @if($showValues && $s['total'] > 0)
                <span class="bar-val">{{ \App\Support\Fmt::ringkas($s['total']) }}</span>
            @endif
            <div class="bar {{ $s['total'] == 0 ? 'zero' : '' }}" style="height: {{ max(2, (int) round($s['total'] / $max * 170)) }}px"></div>
            <span class="bar-label">{{ $s['label'] }}</span>
        </div>
    @endforeach
</div>
