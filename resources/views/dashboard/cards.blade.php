
@php

    $contracted = $projects->where('contract_price', '>', 0)->count();
    $savingsPct = $stats['abc'] > 0 ? round($stats['savings'] / $stats['abc'] * 100, 1) : 0;

    function formatCurrencyShort($amount) {
        if ($amount >= 1000000) {
            return '₱' . number_format($amount / 1000000, 2) . 'M';
        }

        if ($amount >= 1000) {
            return '₱' . number_format($amount / 1000, 2) . 'K';
        }

        return '₱' . number_format($amount, 2);
    }

@endphp

<div class="cards-container">
    <!-- projects -->
    <div class="stat-card">
        <div class="stat-head">
            <span class="stat-title">Total projects</span>
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 14l1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"/>
            </svg>
        </div>
        
        <div class="stat-value">{{ str_pad($stats['total'], 2, '0', STR_PAD_LEFT) }}</div>
        
        <div class="stat-meta">
            <span class="dot"></span>
            <span>{{ $stats['delivered'] }} completed</span>
            <span class="sep">/</span>
            <span>{{ str_pad($stats['ongoing'], 2, '0', STR_PAD_LEFT) }} ongoing</span>
        </div>
    </div>

    <!-- Total ABC -->
    <div class="stat-card">
        <div class="stat-head">
            <span class="stat-title">Approved budget</span>
           <svg class="stat-icon" viewBox="0 0 24 24" fill="none"stroke="currentColor" stroke-width="2"stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 9h18"/><path d="M16 14h.01"/></svg>
        </div>
        
        <div class="stat-value"><div class="stat-value">{{ formatCurrencyShort($stats['abc']) }}</div></div>
        
       <div class="stat-meta">
            <!-- <span class="dot"></span> -->
            <span>Total approved budget across {{ $stats['total'] }} projects</span>
        </div>
    </div>

    <!-- TOtal contract -->
    <div class="stat-card">
        <div class="stat-head">
            <span class="stat-title">Contract value</span>
           <svg class="stat-icon" viewBox="0 0 24 24" fill="none"stroke="currentColor" stroke-width="2"stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h8"/></svg>
        </div>
        
        <div class="stat-value"><div class="stat-value">{{ formatCurrencyShort($stats['contract_price']) }}</div></div>
        
        <div class="stat-meta">
            <span class="dot"></span>
            <span>{{ $contracted }} contracts recorded</span>
            <span class="sep">/</span>
            <span>{{ $stats['total'] - $contracted }} pending</span>
        </div>
    </div>

    <!-- savings -->
    <div class="stat-card">
        <div class="stat-head">
            <span class="stat-title">Procurement savings</span>
            <svg class="stat-icon" viewBox="0 0 24 24" fill="none"stroke="currentColor" stroke-width="2"stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l6 6 4-4 8 8"/><path d="M15 17h6v-6"/></svg>
        </div>
        
        <div class="stat-value">{{ formatCurrencyShort($stats['savings']) }}</div>
        
        <div class="stat-meta">
            <i class="bi bi-check-lg"></i> From recorded contracts only.
        </div>
    </div>
</div>