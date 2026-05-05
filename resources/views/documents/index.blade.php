@extends('layouts.app')

@section('title', 'Dashboard — AI Document Summarizer')
@section('meta_description', 'View all your uploaded documents and their AI-generated summaries, keywords, and insights.')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-2" style="margin-bottom:2rem;">
    <div class="page-header" style="margin-bottom:0">
        <h1>📊 Dashboard</h1>
        <p>Manage your uploaded documents and AI-generated insights</p>
    </div>
    <a href="{{ route('documents.create') }}" class="btn btn-primary">
        ➕ Upload Document
    </a>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Documents</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:var(--success)">{{ $stats['done'] }}</div>
        <div class="stat-label">Processed</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:var(--warning)">{{ $stats['pending'] + $stats['processing'] }}</div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:var(--danger)">{{ $stats['failed'] }}</div>
        <div class="stat-label">Failed</div>
    </div>
</div>

{{-- Documents Table --}}
<div class="card" style="padding:0;">
    <div class="card-header" style="padding:1.25rem 1.5rem; margin-bottom:0;">
        <span class="card-title">All Documents</span>
        <span class="text-muted text-sm">{{ $documents->total() }} total</span>
    </div>

    @if($documents->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <h3>No documents yet</h3>
            <p>Upload your first document to get AI-powered summaries, keywords, and more.</p>
            <a href="{{ route('documents.create') }}" class="btn btn-primary">Upload Document</a>
        </div>
    @else
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table>
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Sentiment</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr>
                        <td>
                            <div class="file-name">{{ $doc->original_name }}</div>
                        </td>
                        <td>
                            <span class="file-type-badge">{{ strtoupper(pathinfo($doc->original_name, PATHINFO_EXTENSION)) }}</span>
                        </td>
                        <td>
                            @php
                                $statusIcons = ['pending'=>'⏳','processing'=>'⚙️','done'=>'✅','failed'=>'❌'];
                            @endphp
                            <span class="badge badge-{{ $doc->status }}">
                                {{ $statusIcons[$doc->status] ?? '' }} {{ ucfirst($doc->status) }}
                            </span>
                        </td>
                        <td>
                            @if($doc->sentiment)
                                <span class="badge badge-{{ $doc->sentiment }}">
                                    {{ ucfirst($doc->sentiment) }}
                                </span>
                            @else
                                <span class="text-muted text-sm">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm text-muted">{{ $doc->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                @if($doc->status === 'done')
                                    <a href="{{ route('documents.show', $doc->id) }}" class="btn btn-outline btn-sm">👁 View</a>
                                @endif
                                <form action="{{ route('documents.destroy', $doc->id) }}" method="POST"
                                      onsubmit="return confirm('Delete this document?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($documents->hasPages())
            <div style="padding:1rem 1.5rem; border-top:1px solid var(--border);">
                {{ $documents->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
