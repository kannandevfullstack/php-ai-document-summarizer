@extends('layouts.app')

@section('title', 'Upload Document — AI Document Summarizer')
@section('meta_description', 'Upload a PDF, DOCX, or TXT file to get AI-powered summaries, keyword extraction, sentiment analysis, and LinkedIn posts.')

@section('content')

<div class="page-header">
    <h1>📤 Upload Document</h1>
    <p>Supported formats: PDF, DOCX, TXT — Max size: 10MB</p>
</div>

<div class="card" style="max-width:680px; margin:0 auto;">
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf

        {{-- Drop Zone --}}
        <div class="upload-zone" id="dropZone">
            <input type="file" name="document" id="fileInput" accept=".pdf,.docx,.txt" required>
            <div class="upload-icon">📄</div>
            <h3 id="dropText">Drag & drop your file here</h3>
            <p>or click to browse from your computer</p>
            <div class="file-types">
                <span class="file-type-badge">PDF</span>
                <span class="file-type-badge">DOCX</span>
                <span class="file-type-badge">TXT</span>
            </div>
        </div>

        {{-- File Preview --}}
        <div id="filePreview" class="hidden mt-2" style="margin-top:1rem;">
            <div class="card" style="padding:1rem; border-color:var(--primary);">
                <div class="flex items-center gap-1">
                    <span id="fileIcon" style="font-size:1.5rem;">📎</span>
                    <div style="flex:1;">
                        <div id="fileName" style="font-weight:600; color:var(--text); font-size:0.9rem;"></div>
                        <div id="fileSize" class="text-sm text-muted"></div>
                    </div>
                    <button type="button" id="removeFile" class="btn btn-danger btn-sm">✕</button>
                </div>
            </div>
        </div>

        {{-- What AI will do --}}
        <div class="mt-2" style="margin-top:1.5rem; margin-bottom:1.5rem;">
            <p class="text-sm text-muted" style="margin-bottom:0.75rem; font-weight:500;">🤖 AI will generate:</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem;">
                <div class="flex gap-1 items-center text-sm text-muted">✅ Document Summary</div>
                <div class="flex gap-1 items-center text-sm text-muted">✅ Keyword Extraction</div>
                <div class="flex gap-1 items-center text-sm text-muted">✅ Sentiment Analysis</div>
                <div class="flex gap-1 items-center text-sm text-muted">✅ LinkedIn Post</div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary btn-lg w-full" id="submitBtn" disabled>
            <span id="btnText">🚀 Upload & Analyze</span>
            <span id="btnSpinner" class="spinner hidden"></span>
        </button>
    </form>
</div>

@endsection

@push('scripts')
<script>
const dropZone   = document.getElementById('dropZone');
const fileInput  = document.getElementById('fileInput');
const filePreview= document.getElementById('filePreview');
const dropText   = document.getElementById('dropText');
const fileName   = document.getElementById('fileName');
const fileSize   = document.getElementById('fileSize');
const fileIcon   = document.getElementById('fileIcon');
const submitBtn  = document.getElementById('submitBtn');
const removeBtn  = document.getElementById('removeFile');
const uploadForm = document.getElementById('uploadForm');
const btnText    = document.getElementById('btnText');
const btnSpinner = document.getElementById('btnSpinner');
const overlay    = document.getElementById('loadingOverlay');

const icons = { pdf: '📕', docx: '📘', doc: '📘', txt: '📄' };

function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function showFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    fileName.textContent = file.name;
    fileSize.textContent = formatBytes(file.size);
    fileIcon.textContent = icons[ext] || '📎';
    filePreview.classList.remove('hidden');
    dropZone.style.borderColor = 'var(--primary)';
    submitBtn.disabled = false;
}

function clearFile() {
    fileInput.value = '';
    filePreview.classList.add('hidden');
    dropText.textContent = 'Drag & drop your file here';
    dropZone.style.borderColor = '';
    submitBtn.disabled = true;
}

fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) showFile(fileInput.files[0]);
});

removeBtn.addEventListener('click', clearFile);

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        showFile(file);
    }
});

uploadForm.addEventListener('submit', () => {
    submitBtn.disabled = true;
    btnText.classList.add('hidden');
    btnSpinner.classList.remove('hidden');
    overlay.classList.add('show');
});
</script>
@endpush
