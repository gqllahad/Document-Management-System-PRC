@php
    // $stageCounts e.g. ['pend' => 1, 'bidding' => 1, 'obligated' => 1, 'installed' => 1, 'delivered' => 1, 'cancelled' => 1]
    $stages = [
        ['label' => 'Pending/Ongoing', 'count' => $stageCounts['pend'] ?? 0,            'color' => '#9db8e0'],
        ['label' => 'Successful Bid',         'count' => $stageCounts['bidding'] ?? 0,        'color' => '#b8902a'],
        ['label' => 'Obligated',         'count' => $stageCounts['obligated'] ?? 0,        'color' => '#1f6fe0'],
        ['label' => 'Installed/Renewed', 'count' => $stageCounts['installed'] ?? 0,       'color' => '#0f2a5c'],
        ['label' => 'Delivered',  'count' => $stageCounts['delivered'] ?? 0, 'color' => '#6b7280'],
        ['label' => 'Cancelled',       'count' => $stageCounts['cancelled'] ?? 0,      'color' => '#1f8a5b'],
        ['label' => 'Submitted DV',       'count' => $stageCounts['submitted'] ?? 0,      'color' => '#130c3b'],
    ];

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

<div class="stages-panel">
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
        <div class="stages-pie" @style(['background: conic-gradient(' . $gradient . ')'])></div>
        <ul class="stages-list">
            @foreach ($stages as $s)
                <li>
                    <span class="stages-dot" @style(['background:' . $s['color']])></span>
                    {{ $s['label'] }}
                    <span class="stages-count">{{ $s['count'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="stages-total">
        <span><strong>{{ $total }}</strong>Total projects</span>
        <span class="stages-done">✓ {{ $stageCounts['completed'] ?? 0 }} completed</span>
    </div>

</div>