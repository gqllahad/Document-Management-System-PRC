@extends('layouts.shared_division')

@section('title', 'Network Infrastructure and Information Security Division')
@section('division', 'NIISD')

@section('main-content')
     <div class="page-head">
        <div>
            <h2>Dashboard</h2>
            <p>{{ Str::upper($division->name) }} - Procurement Projects Monitoring System </p>
        </div>
    </div>

    <div class="card">Summary cards coming here</div>
@endsection