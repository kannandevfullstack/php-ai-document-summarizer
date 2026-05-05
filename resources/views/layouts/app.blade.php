<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'AI-powered document summarizer — upload PDFs, DOCX and TXT files to get instant summaries, keywords, sentiment analysis and LinkedIn posts.')">
    <title>@yield('title', 'AI Document Summarizer')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="page-wrapper">

    {{-- Navbar --}}
    <nav class="navbar">
        <div class="container navbar-inner">
            <a href="{{ route('documents.index') }}" class="navbar-brand">
                <div class="brand-icon">🤖</div>
                <span>AI Summarizer</span>
            </a>
            <ul class="navbar-nav">
                @auth
                    <li><a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.index') ? 'active' : '' }}">📊 Dashboard</a></li>
                    <li><a href="{{ route('documents.create') }}" class="{{ request()->routeIs('documents.create') ? 'active' : '' }}">➕ Upload</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" style="margin-left:0.5rem; padding: 0.35rem 0.6rem;">Logout</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Log in</a></li>
                    <li><a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    {{-- Flash Messages --}}
    <div class="container" style="margin-top:1rem;">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">❌ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <div>
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

</div>

{{-- Loading Overlay --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <p id="loadingMessage">Processing your document with AI...</p>
</div>

@stack('scripts')
</body>
</html>
