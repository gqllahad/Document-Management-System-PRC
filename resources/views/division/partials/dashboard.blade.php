@extends('layouts.default_main')

@section('title', 'Procurement Overview')

@section('sidebar-class', 'sidebar-admin')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('main-content')

<div class="dashboard-header">
    <div class="dash-eyebrow">
        <small>PROCUREMENT PROJECT MONITORING</small>
    </div>

    <div class="expo-head" style="display:flex; justify-content:space-between;">
        <h1 class="dash-title">Procurement overview</h1>
        <button type="button" class="btn-export" id="exportSummary">
            <i class="bi bi-download"></i>
            <span>Export Summary</span>
        </button>
    </div>
    
    <p class="dash-subtitle">Track budgets, requisitions, and project milestones across all active procurements.</p>

</div>

<hr style="opacity: 20%;">


<div style="display:flex;flex-direction:row;padding: 10px 20px;gap:min(1.5rem);align-items:center;">
    <label for=""><small><i class="bi bi-sliders"></i> Overview for</small></label>
    @if(auth()->user()->role === 'admin')
        <div>
            <label for="div"><small>Division</small></label>
            <select name="div" id="divisionFilter" class="filter-select">
                <option value="all">All divisions</option>
                @foreach ($divisions as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div>
        <label for="fiscal"><small>Fiscal year</small></label>
        <select name="fiscal" id="fiscalFilter" class="filter-select">
            <option value="all">All</option>
            @foreach ($fiscalYears as $fy)
                <option value="{{ $fy }}" @selected($filters['fiscal'] === $fy)>{{ $fy }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="qt"><small>Quarter</small></label>
        <select name="quarter" id="quarterFilter" class="filter-select">
            <option value="all">All quarters</option>
            @foreach ($quarters as $q)
                <option value="{{ $q }}" @selected($filters['quarter'] === $q)>{{ $q }}</option>
            @endforeach
        </select>
    </div>
</div>

<hr style="opacity: 20%;">

 @include('dashboard.cards')
<div class="dash-grid">

    @include('dashboard.stage-panel')

    @include('dashboard.budget-panel')

    @include('dashboard.attention-panel')

    @include('dashboard.delivery-panel')
</div>

    
@endsection