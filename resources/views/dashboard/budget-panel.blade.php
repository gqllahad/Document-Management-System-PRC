@php
    $quarters = ['EPA' => 'EPA', '1ST' => 'Q1', '2ND' => 'Q2', '3RD' => 'Q3', '4TH' => 'Q4'];

    $max  = max(1, (float) $byQuarter->max(fn ($r) => max($r->abc, $r->contract)));
    $step = 10 ** (strlen((string) (int) $max) - 1);
    $top  = ceil($max / $step) * $step;

    $m   = fn ($v) => '₱' . number_format($v / 1e6, 1) . 'M';
    $pct = fn ($v) => round($v / $top * 100, 1);

    $title = $filters['division'] === 'all'
        ? 'All divisions'
        : $divisions->firstWhere('id', $filters['division'])?->name;
@endphp

<div class="bars-panel" data-bars='@json($barRows)'>
    <div class="bars-head">
        <div>
            <h3 class="stages-title">ABC vs. contract price</h3>
            <p class="stages-sub">{{ $title }} · by quarter</p>
        </div>
        <div class="bars-legend">
            <span><i class="bar-abc"></i>ABC</span>
            <span><i class="bar-con"></i>Contract</span>
        </div>
    </div>

    <div class="bars-chart">
        <div class="bars-axis">
            @for ($i = 4; $i >= 0; $i--)
                <span>{{ $m($top * $i / 4) }}</span>
            @endfor
        </div>

        <div class="bars-main">
            <div class="bars-plot">
                @foreach ($quarters as $key => $label)
                    @php $r = $byQuarter[$key] ?? null; @endphp
                    <div class="bars-pair" data-q="{{ $key }}">
                        <i class="bar-abc" @style(['height: ' . $pct($r->abc ?? 0) . '%']) title="ABC {{ $m($r->abc ?? 0) }}"></i>
                        <i class="bar-con" @style(['height: ' . $pct($r->contract ?? 0) . '%']) title="Contract {{ $m($r->contract ?? 0) }}"></i>
                    </div>
                @endforeach
            </div>
            <div class="bars-x">
                @foreach ($quarters as $label) <span>{{ $label }}</span> @endforeach
            </div>
        </div>
    </div>
</div>