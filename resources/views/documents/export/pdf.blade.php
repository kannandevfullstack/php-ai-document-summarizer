<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $document->original_name }} - AI Summary</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        h1 { color: #4f46e5; border-bottom: 2px solid #e0e7ff; padding-bottom: 0.5rem; }
        h2 { color: #1e293b; margin-top: 2rem; border-bottom: 1px solid #cbd5e1; padding-bottom: 0.25rem; }
        .meta { color: #64748b; font-size: 0.9rem; margin-bottom: 2rem; }
        .badge { background: #e0e7ff; color: #4f46e5; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; display: inline-block; margin-right: 0.5rem; margin-bottom: 0.5rem; }
        .sentiment-positive { color: #16a34a; font-weight: bold; }
        .sentiment-negative { color: #dc2626; font-weight: bold; }
        .sentiment-neutral { color: #64748b; font-weight: bold; }
        .content { margin-top: 1rem; }
        pre { background: #f8fafc; padding: 1rem; border: 1px solid #e2e8f0; border-radius: 4px; white-space: pre-wrap; font-family: inherit; }
    </style>
</head>
<body>
    <h1>AI Document Summary</h1>
    <div class="meta">
        <strong>File:</strong> {{ $document->original_name }}<br>
        <strong>Generated:</strong> {{ $document->updated_at->format('M d, Y H:i') }}
    </div>

    <h2>📋 Summary</h2>
    <div class="content">
        {!! nl2br(e($document->summary)) !!}
    </div>

    <h2>🔑 Keywords</h2>
    <div class="content">
        @if($document->keywords && is_array($document->keywords))
            @foreach($document->keywords as $keyword)
                <span class="badge">{{ $keyword }}</span>
            @endforeach
        @else
            No keywords extracted.
        @endif
    </div>

    <h2>💬 Sentiment Analysis</h2>
    <div class="content">
        Tone: <span class="sentiment-{{ strtolower($document->sentiment) }}">{{ ucfirst($document->sentiment) }}</span> 
        (Confidence: {{ round(($document->sentiment_score ?? 0) * 100) }}%)
    </div>

    <h2>💼 Suggested LinkedIn Post</h2>
    <div class="content">
        <pre>{{ $document->linkedin_post }}</pre>
    </div>
</body>
</html>
