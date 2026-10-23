@extends('layouts.shared_division')

@section('title', 'Information and Communication Technology Service - Office of Director')
@section('division', 'ICTS-OD')

@section('sidebar-class', auth()->user()->role === 'admin' ? 'sidebar-admin' : 'sidebar-divisions')
@section('sidebar')
    @include(auth()->user()->role === 'admin' ? 'partials.sidebar-admin' : 'partials.sidebar-divisions')
@endsection

@section('main-content')
    @include('division.partials.tracking')
@endsection