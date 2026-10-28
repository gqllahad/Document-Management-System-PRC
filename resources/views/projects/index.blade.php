@extends('layouts.shared_division')

@section('title', 'Procurement Projects')

@section('sidebar-class', auth()->user()->role === 'admin' ? 'sidebar-admin' : 'sidebar-division')
@section('sidebar') @include(auth()->user()->role === 'admin' ? 'partials.sidebar-admin' : 'partials.sidebar-divisions') @endsection

@section('division', Str::upper(auth()->user()->division->name ?? ''))

@php $isAdmin = auth()->user()->role === 'admin'; @endphp

@section('main-content')
<div class="page-head">

    <div>
        <h2>Projects / Items</h2> 
        <!-- <span class="count">{{ $projects->count() }}</span> -->
        <p>{{ auth()->user()->role === 'admin' ? 'All divisions' : Str::upper(auth()->user()->division->name) }}</p>
    </div>


    <div class="new-project">
        @if (!$isAdmin)
            <button type="button" class="btn-primary" data-drawer-open="projectDrawer">
                <i class="bi bi-plus-lg"></i> Create new project
            </button>
        @endif
    </div>

</div>

<div class="card table-card">
    <div class="table-head">
        <div>
            <h3>Projects / Items</h3>
             <small>Manage All Projects / Items</small>
        </div>
        
        <div class="table-tools">

            <div class="tools-top">
                @if ($isAdmin)
                    <!-- <select id="divisionFilter" class="filter-select">
                        <option value="all">All Divisions</option>
                        @foreach ($divisions as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select> -->
                    @include('projects.filter.adminFilter')
                @else

                        @include('projects.filter.userFilter')

                @endif

                <!-- <select id="fiscalFilter" class="filter-select">
                    <option value="all">All Fiscal Years</option>
                    @foreach ($fiscalYears as $fy)
                        <option value="{{ $fy }}">{{ $fy }}</option>
                    @endforeach
                </select>

                <select id="quarterFilter" class="filter-select">
                    <option value="all">All Quarters</option>
                    @foreach ($quarters as $qt)
                        <option value="{{ $qt }}">{{ $qt }}</option>
                    @endforeach
                </select>

                <select id="typeFilter" class="filter-select">
                    <option value="all">All Funding Source</option>
                    <option value="MMOE - Current Fund">MMOE - Current F.</option>
                    <option value="MMOE - Continuing Fund">MMOE - Continuing F.</option>
                    <option value="Capital Outlay - Current Fund">Capital Outlay - Current F.</option>
                    <option value="Capital Outlay - Continuing Fund">Capital Outlay - Continuing F.</option>
                </select> -->
                

            </div>
           
            <!-- <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" id="projectSearch" placeholder="Search project..">
            </div> -->
            
        </div>
            
    </div>

    @include('projects.filter.defaultFilter')

    <div class="table-wrap">
        <table class="table" id="projectTable">
            @include('projects.table')
        </table>
    </div>
</div>

@php $stage1Err = $errors->has('project_name') || $errors->has('approved_budget'); @endphp

<div class="drawer-backdrop {{ $stage1Err ? 'open' : '' }}" id="projectDrawerBackdrop"></div>

<!-- view -->
 @include('projects.partials.view')

 <!-- remarks (admin) -->
@include('projects.partials.remarks')

@if (auth()->user()->role !== 'admin')
<!-- stage 1 -->
@include('projects.stages.stage1')

<!-- stage 2 -->
@include('projects.stages.stage2')

<!-- stage 3 -->
 @include('projects.stages.stage3')

 <!-- modify -->
@include('projects.stages.modify-stage')


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


