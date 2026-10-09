@extends('layouts.default_main')

@section('title', 'Admin Dashboard')

@section('sidebar-class', 'sidebar-admin')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('main-content')
    <div class="card">Dashboard content here</div>
@endsection