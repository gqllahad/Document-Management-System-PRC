@extends('layouts.shared_division')

@section('title', 'Systems Development and Maintenance Division')
@section('division', 'SDMD')
@section('sidebar-class', auth()->user()->role === 'admin' ? 'sidebar-admin' : 'sidebar-divisions')
@section('sidebar')
    @include(auth()->user()->role === 'admin' ? 'partials.sidebar-admin' : 'partials.sidebar-divisions')
@endsection

@section('main-content')
     <!-- <div class="page-head">
        <div>
            <h2>Dashboard</h2>
            <p>{{ Str::upper($division->name) }} - Procurement Projects Monitoring System </p>
        </div>
    </div>

    <div class="card">Summary cards coming here</div> -->
    @include('division.partials.dashboard')
@endsection