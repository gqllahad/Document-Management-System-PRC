
@extends('layouts.default')

@section('header')
@endsection

@section('main-content')

<div class="login-page">
    
    <div class="login-brand">

        <div class="brand-content">

            <div class="brand-logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="">
            </div>

            <h1>Document Management System</h1>

            <p>
                Securely manage, organize, and access your documents
                in one centralized platform.
            </p>

        </div>

    </div>

    <div class="login-section">
        <div class="login-card">
            <div class="login-header">
                <span class="login-label">
                    WELCOME BACK
                </span>
                <h2>Sign in</h2>
                <p>
                    Enter your credentials to continue.
                </p>
            </div>

            <!-- error handling -->
            @if ($errors->any())
                <p>{{ $errors->first() }}</p>
            @endif

            <form action="{{ route('form-login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="login-button">
                    Sign In
                </button>
            </form>

            <div class="register-link">
                <span>Don't have an account?</span>
                <a href="{{ route('register') }}">
                    Create an account
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
@endsection

