@extends('layouts.shared_division')

@section('title', 'Procurement Projects')
@section('sidebar-class', auth()->user()->role === 'admin' ? 'sidebar-admin' : 'sidebar-division')
@section('sidebar') @include(auth()->user()->role === 'admin' ? 'partials.sidebar-admin' : 'partials.sidebar-divisions') @endsection
@section('division', Str::upper(auth()->user()->division->name ?? ''))

@section('main-content')
<div class="page-head">
    <div>
        <h2>Projects</h2>
        <p>{{ auth()->user()->role === 'admin' ? 'All divisions' : Str::upper(auth()->user()->division->name) }}</p>
    </div>

</div>

@php $isAdmin = auth()->user()->role === 'admin'; @endphp

<div class="card table-card">
    <div class="table-head">
        <div>
            <h3>Projects<span class="count">{{ $projects->total() }}</span></h3>
             <small>Manage all procurement projects</small>
        </div>
        <div class="table-tools">
            @if (!$isAdmin)
                <button type="button" class="btn-primary" data-drawer-open="projectDrawer">
                    <i class="bi bi-plus-lg"></i> New project
                </button>
            @endif

            <div class="tools-top">
                @if ($isAdmin)
                    <select id="divisionFilter" class="filter-select">
                        <option value="all">All divisions</option>
                        @foreach ($divisions as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                @endif

                <select id="quarterFilter" class="filter-select">
                    <option value="all">All quarters</option>
                    @foreach ($quarters as $qt)
                        <option value="{{ $qt }}">{{ $qt }}</option>
                    @endforeach
                </select>

                <select id="typeFilter" class="filter-select">
                    <option value="all">All source funds</option>
                    <option value="MMOE - Current Fund">MMOE - Current F.</option>
                    <option value="MMOE - Continuing Fund">MMOE - Continuing F.</option>
                    <option value="Capital Outlay - Current Fund">Capital Outlay - Current F.</option>
                    <option value="Capital Outlay - Continuing Fund">Capital Outlay - Continuing F.</option>
                </select>

            </div>

            <!-- <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" id="projectSearch" placeholder="Search project..">
            </div> -->
        </div>
            
    </div>

    <div class="tools-bottom">
        <div class="filter-buttons">
            <button type="button" class="active" data-filter="all">All projects <span>{{ $counts['all'] }}</span></button>
            <button type="button" data-filter="pending">Pending contract <span>{{ $counts['pending'] }}</span></button>
            <button type="button" data-filter="recorded">Contract recorded <span>{{ $counts['recorded'] }}</span></button>
        </div>

        <div class="search">
            <i class="bi bi-search"></i>
            <input type="search" id="projectSearch" placeholder="Search project..">
        </div>
    </div>

    <div class="table-wrap">
        <table class="table" id="projectTable">
            @include('projects.table')
        </table>
    </div>
</div>

<div class="pagination-wrap">{{ $projects->links() }}</div>

@php $stage1Err = $errors->has('project_name') || $errors->has('approved_budget'); @endphp

<div class="drawer-backdrop {{ $stage1Err ? 'open' : '' }}" id="projectDrawerBackdrop"></div>

<!-- view -->
 @include('projects.partials.view')

@if (auth()->user()->role !== 'admin')
<!-- stage 1 -->
@include('projects.stages.stage1')

<!-- stage 2 -->
@include('projects.stages.stage2')

<!-- stage 3 -->
 @include('projects.stages.stage3')

<!-- review -->
<dialog class="review" id="reviewModal">
    <div class="review-head">
        <span class="review-ico"><i class="bi bi-clipboard-check"></i></span>
        <div>
            <h3>Review before saving</h3>
            <p>Please double-check the details below.</p>
        </div>
    </div>

    <div class="review-body">
        <div class="review-warn" id="rvWarn" hidden>
            <i class="bi bi-exclamation-triangle"></i><span></span>
        </div>
        <dl class="review-list" id="rvList"></dl>
    </div>

    <div class="review-foot">
        <button type="button" class="btn-sm" data-review-back>Go back &amp; edit</button>
        <button type="button" class="btn-primary" data-review-confirm><i class="bi bi-check-lg"></i> Confirm &amp; save</button>
    </div>
</dialog>
@endif

@endsection


