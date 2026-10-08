@extends('layouts.default')

@section('main-content')

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="">
            </div>
            <h1>Create an account</h1>
            <p>
                Register to access the document management system.
            </p>
        </div>

        @if ($errors->any())
            <p>{{ $errors->first() }}</p>
        @endif
        
        <form action="{{ route('form-register') }}" method="POST" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required>
            </div>

            <div class="form-group">
                <label for="division">Division</label>
                <select name="division_id" id="division_id">
                    <option value="" disabled selected>Select your division..</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}">
                            {{ Str::upper($division->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="auth-button">
                Create Account
            </button>
        </form>

        <div class="auth-footer">
            <span>Already have an account?</span>
            <a href="{{ route('home') }}">Sign in </a>
        </div>
    </div>
</div>

@endsection

