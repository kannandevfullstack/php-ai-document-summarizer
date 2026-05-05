@extends('layouts.app')

@section('title', 'Forgot Password - AI Document Summarizer')

@section('content')
<div class="card" style="max-width: 400px; margin: 2rem auto;">
    <div class="card-header">
        <span class="card-title">🔒 Forgot Password</span>
    </div>
    <p class="text-sm text-muted mb-2">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group mt-2">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-full mt-3" style="justify-content:center;">Email Password Reset Link</button>
    </form>
    <div class="text-center mt-3 text-sm text-muted">
        Remember your password? <a href="{{ route('login') }}">Log in</a>
    </div>
</div>
@endsection
