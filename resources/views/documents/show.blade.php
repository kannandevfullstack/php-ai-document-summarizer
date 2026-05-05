@extends('layouts.app')

@section('title', $document->original_name . ' — AI Document Summarizer')
@section('meta_description', 'AI-generated summary, keywords, sentiment and LinkedIn post for ' . $document->original_name)

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between" style="margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <div class="flex items-center gap-1" style="margin-bottom:0.35rem;">
            <a href="{{ route('documents.index') }}" class="text-muted text-sm">← Dashboard</a>
        </div>
        <h1 style="font-size:1.4rem; font-weight:700; color:var(--text); word-break:break-word;">
            {{ $document->original_name }}
        </h1>
        <div class="flex items-center gap-1 mt-1">
            <span class="file-type-badge">{{ strtoupper(pathinfo($document->original_name, PATHINFO_EXTENSION)) }}</span>
            <span class="badge badge-{{ $document->status }}">{{ ucfirst($document->status) }}</span>
            @if($document->sentiment)
                <span class="badge badge-{{ $document->sentiment }}">{{ ucfirst($document->sentiment) }}</span>
            @endif
            <span class="text-muted text-sm">{{ $document->created_at->format('M d, Y H:i') }}</span>
        </div>
    </div>

    {{-- Export Buttons --}}
    @if($document->status === 'done')
    <div class="flex gap-1">
        <a href="{{ route('documents.export.pdf', $document->id) }}" class="btn btn-outline btn-sm">📄 Export PDF</a>
        <a href="{{ route('documents.export.markdown', $document->id) }}" class="btn btn-outline btn-sm">📝 Export MD</a>
        <form action="{{ route('documents.destroy', $document->id) }}" method="POST"
              onsubmit="return confirm('Delete this document?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
        </form>
    </div>
    @endif
</div>

@if($document->status === 'done')

{{-- Tabs --}}
<div class="tabs" id="tabs">
    <button class="tab-btn active" data-tab="summary">📋 Summary</button>
    <button class="tab-btn" data-tab="keywords">🔑 Keywords</button>
    <button class="tab-btn" data-tab="sentiment">💬 Sentiment</button>
    <button class="tab-btn" data-tab="linkedin">💼 LinkedIn Post</button>
    <button class="tab-btn" data-tab="rawtext">📄 Raw Text</button>
</div>

{{-- Summary Tab --}}
<div class="tab-pane active result-section" id="tab-summary">
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 AI Summary</span>
        </div>
        <div class="summary-text">{{ $document->summary }}</div>
    </div>
</div>

{{-- Keywords Tab --}}
<div class="tab-pane result-section" id="tab-keywords">
    <div class="card">
        <div class="card-header">
            <span class="card-title">🔑 Keywords</span>
            <span class="text-muted text-sm">{{ count($document->keywords ?? []) }} extracted</span>
        </div>
        @if($document->keywords)
            <div class="keywords-list">
                @foreach($document->keywords as $keyword)
                    <span class="keyword-chip">{{ $keyword }}</span>
                @endforeach
            </div>
        @else
            <p class="text-muted text-sm">No keywords extracted.</p>
        @endif
    </div>
</div>

{{-- Sentiment Tab --}}
<div class="tab-pane result-section" id="tab-sentiment">
    <div class="card">
        <div class="card-header">
            <span class="card-title">💬 Sentiment Analysis</span>
        </div>
        @if($document->sentiment)
            <div style="max-width:480px;">
                <div class="flex items-center gap-1" style="margin-bottom:1.5rem;">
                    <span style="font-size:2.5rem;">
                        @if($document->sentiment === 'positive') 😊
                        @elseif($document->sentiment === 'negative') 😔
                        @else 😐
                        @endif
                    </span>
                    <div>
                        <div style="font-size:1.5rem; font-weight:700; color:var(--text);">
                            {{ ucfirst($document->sentiment) }}
                        </div>
                        <div class="text-muted text-sm">Overall tone of the document</div>
                    </div>
                </div>

                <div class="sentiment-bar-wrap">
                    <div class="sentiment-label">
                        <span style="font-weight:500; color:var(--text);">Confidence</span>
                        <span style="color:var(--{{ $document->sentiment === 'positive' ? 'success' : ($document->sentiment === 'negative' ? 'danger' : 'neutral') }})">
                            {{ round(($document->sentiment_score ?? 0.5) * 100) }}%
                        </span>
                    </div>
                    <div class="sentiment-bar">
                        <div class="sentiment-fill {{ $document->sentiment }}"
                             style="width:{{ round(($document->sentiment_score ?? 0.5) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        @else
            <p class="text-muted text-sm">Sentiment not analyzed.</p>
        @endif
    </div>
</div>

{{-- LinkedIn Post Tab --}}
<div class="tab-pane result-section" id="tab-linkedin">
    <div class="card">
        <div class="card-header">
            <span class="card-title">💼 LinkedIn Post</span>
        </div>
        @if($document->linkedin_post)
            <div class="linkedin-post" id="linkedinContent">
                <button class="btn btn-outline btn-sm copy-btn" id="copyBtn" onclick="copyLinkedIn()">📋 Copy</button>
                {{ $document->linkedin_post }}
            </div>
        @else
            <p class="text-muted text-sm">LinkedIn post not generated.</p>
        @endif
    </div>
</div>

{{-- Raw Text Tab --}}
<div class="tab-pane result-section" id="tab-rawtext">
    <div class="card">
        <div class="card-header">
            <span class="card-title">📄 Extracted Text</span>
            <span class="text-muted text-sm">{{ number_format(strlen($document->extracted_text ?? '')) }} characters</span>
        </div>
        <div class="raw-text-toggle" onclick="toggleRaw()">
            <span id="rawToggleIcon">▶</span>
            <span>Show extracted text</span>
        </div>
        <div class="raw-text-box" id="rawTextBox">{{ $document->extracted_text }}</div>
    </div>
</div>

@elseif($document->status === 'processing')
    <div class="alert alert-info">⚙️ Your document is being processed. Please refresh in a few seconds.</div>
@elseif($document->status === 'pending')
    <div class="alert alert-info">⏳ Your document is queued for processing.</div>
@elseif($document->status === 'failed')
    <div class="alert alert-danger">❌ Processing failed. Please try uploading again.</div>
@endif

@endsection

@push('scripts')
<script>
// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});

// Copy LinkedIn
function copyLinkedIn() {
    const text = document.getElementById('linkedinContent').innerText.replace('📋 Copy', '').trim();
    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyBtn');
        btn.textContent = '✅ Copied!';
        setTimeout(() => btn.textContent = '📋 Copy', 2000);
    });
}

// Toggle raw text
function toggleRaw() {
    const box = document.getElementById('rawTextBox');
    const icon = document.getElementById('rawToggleIcon');
    const isOpen = box.style.display === 'block';
    box.style.display = isOpen ? 'none' : 'block';
    icon.textContent = isOpen ? '▶' : '▼';
}
</script>
@endpush
