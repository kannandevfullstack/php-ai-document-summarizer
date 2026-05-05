@extends('layouts.app')

@section('title', 'Login - AI Document Summarizer')

@section('content')
<div class="card" style="max-width: 400px; margin: 2rem auto;">
    <div class="card-header">
        <span class="card-title">🔑 Login</span>
    </div>
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="flex items-center justify-between mt-2">
            <label class="flex items-center gap-1 text-sm text-muted">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary w-full mt-3" style="justify-content:center;">Log in</button>
    </form>
    <div class="text-center mt-3 text-sm text-muted">
        Don't have an account? <a href="{{ route('register') }}">Register</a>
    </div>
</div>
@endsection
