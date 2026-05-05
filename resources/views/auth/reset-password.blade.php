@extends('layouts.app')

@section('title', 'Reset Password - AI Document Summarizer')

@section('content')
<div class="card" style="max-width: 400px; margin: 2rem auto;">
    <div class="card-header">
        <span class="card-title">🔄 Reset Password</span>
    </div>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $email ?? old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-full mt-3" style="justify-content:center;">Reset Password</button>
    </form>
</div>
@endsection
