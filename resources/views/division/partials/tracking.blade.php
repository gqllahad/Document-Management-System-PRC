@extends('layouts.default_main')

@section('title', 'Track delivery')
@section('sidebar-class', 'sidebar-admin')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('main-content')
<div class="page-head">
    <div>
        <nav class="crumbs"><a href="{{ route('admin.dashboard') }}">Procurement</a> <i class="bi bi-chevron-right"></i> <span>Track delivery</span></nav>
        <h2>Track delivery <span class="count">{{ $deliveries->count() }} scheduled</span></h2>
        <p>Notice-to-proceed timelines and delivery deadlines.</p>
    </div>
    <small class="asof"><i class="dot"></i> Schedule as of {{ now()->format('M j, Y') }}</small>
</div>

<div class="stat-cards">
    <div class="card stat">
        <div><small>Scheduled deliveries</small><strong>{{ str_pad($deliveries->count(), 2, '0', STR_PAD_LEFT) }}</strong><span>Currently in progress</span></div>
        <i class="bi bi-truck ico is-blue"></i>
    </div>
    <div class="card stat">
        <div><small>Due within 14 days</small><strong>{{ str_pad($soon, 2, '0', STR_PAD_LEFT) }}</strong><span>{{ $soon ? 'Needs follow-up' : 'No upcoming deadlines' }}</span></div>
        <i class="bi bi-clock ico is-amber"></i>
    </div>
    <div class="card stat">
        <div><small>Overdue deliveries</small><strong>{{ str_pad($overdue, 2, '0', STR_PAD_LEFT) }}</strong><span>{{ $overdue ? 'Past deadline' : 'No missed deadlines' }}</span></div>
        <i class="bi bi-check2-all ico is-green"></i>
    </div>
</div>

@include('dashboard.delivery-panel', ['mode' => 'full'])
@endsection