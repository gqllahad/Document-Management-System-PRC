@extends('layouts.default_main')

@if (auth()->user()->role === 'admin')
    @section('sidebar-class', 'sidebar-admin')
    @section('sidebar') @include('partials.sidebar-admin') @endsection
@else
    @section('sidebar-class', 'sidebar-division')
    @section('sidebar') @include('partials.sidebar-divisions') @endsection
@endif