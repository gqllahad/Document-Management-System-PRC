@php
    $colors = [
        'Pending/Ongoing'   => '#9db8e0',
        'Successful Bid'    => '#b8902a',
        'Obligated'         => '#1f6fe0',
        'Installed/Renewed' => '#0f2a5c',
        'Delivered'         => '#6b7280',
        'Cancelled'         => '#c0392b',
        'Submitted DV'      => '#130c3b',
    ];

    $stages = $statuses->map(fn ($s) => [
        'id'    => $s->id,
        'label' => $s->name,
        'count' => (int) ($stageCounts[$s->id] ?? 0),
        'color' => $colors[$s->name] ?? '#cbd5e1',
    ])->all();

    $total = array_sum(array_column($stages, 'count'));
    $acc = 0;
    $parts = [];
    foreach ($stages as $s) {
        $start = $total ? $acc / $total * 100 : 0;
        $acc += $s['count'];
        $end = $total ? $acc / $total * 100 : 0;
        if ($end > $start) $parts[] = "{$s['color']} {$start}% {$end}%";
    }
    $gradient = $total ? implode(', ', $parts) : '#e5e7eb 0 100%';
@endphp

<div class="stages-panel" data-donut data-stages='@json($stageByDivision)'>
    <div class="stages-head">
        <div>
            <h3 class="stages-title">Procurement stages</h3>
            <p class="stages-sub">Current stage of each project</p>
        </div>
        <svg class="stages-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 14l1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"/>
        </svg>
    </div>

    <div class="stages-body">
        <div class="stages-pie" @style(['background: conic-gradient(' . $gradient . ')'])>
            <div class="stages-hole">
                <strong class="stages-num">{{ $total }}</strong>
                <small class="stages-lbl">All projects</small>
            </div>
        </div>

        <ul class="stages-list">
            @foreach ($stages as $s)
                <li tabindex="0"
                    data-id="{{ $s['id'] }}"
                    data-label="{{ $s['label'] }}"
                    data-count="{{ $s['count'] }}"
                    data-color="{{ $s['color'] }}">
                    <span class="stages-dot" @style(['background:' . $s['color']])></span>
                    {{ $s['label'] }}
                    <span class="stages-count">{{ $s['count'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>

</div>