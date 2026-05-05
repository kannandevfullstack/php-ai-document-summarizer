@extends('layouts.app')

@section('title', 'Register - AI Document Summarizer')

@section('content')
<div class="card" style="max-width: 400px; margin: 2rem auto;">
    <div class="card-header">
        <span class="card-title">📝 Register</span>
    </div>
    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-full mt-3" style="justify-content:center;">Register</button>
    </form>
    <div class="text-center mt-3 text-sm text-muted">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </div>
</div>
@endsection
