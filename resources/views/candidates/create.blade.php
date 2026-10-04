@extends('layouts.app')

@section('title', 'Tambah Kandidat')

@section('content')
<div class="page-header" style="align-items: center; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 14px;">
        <a href="{{ route('procurements.show', $procurement) }}" class="btn-back" title="Kembali ke Pengadaan">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div>
            <h1 class="page-title">Tambah Kandidat Iklan Produk</h1>
            <div class="page-header-sub">Pengadaan: {{ $procurement->name }}</div>
        </div>
    </div>
</div>

<div style="max-width: 680px;">
    @if($procurement->criteria->isEmpty())
        <div style="background: var(--warning-bg); border: 1px solid var(--warning-border); border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" style="flex-shrink:0; margin-top: 1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <div style="font-size: 12.5px; color: var(--text);">
                <strong>Perhatian:</strong> Pengadaan ini belum memiliki kriteria target. <a href="{{ route('procurements.show', $procurement) }}" style="color: var(--warning); text-decoration: underline; font-weight: 600;">Tambahkan kriteria penilaian terlebih dahulu</a> agar AI dapat memberikan penilaian yang akurat.
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('procurements.candidates.store', $procurement) }}" enctype="multipart/form-data" id="candidate-form">
        @csrf

        {{-- Section 1: Informasi Dasar --}}
        <div class="form-section">
            <div class="form-section-header green">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Informasi Produk
            </div>
            <div class="form-section-body">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="name">Nama Kandidat / Model Barang <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" placeholder="Contoh: Lenovo ThinkPad E14 Gen 4 / ASUS Zenbook 14" autofocus required>
                    <div class="form-hint">Nama atau model produk yang akan dianalisis dan dibandingkan oleh AI.</div>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Screenshot Iklan --}}
        <div class="form-section">
            <div class="form-section-header purple">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M20.4 14.5L16 10 4 20"/></svg>
                Screenshot Iklan Marketplace (1 - 3 Gambar)
                <span class="ai-badge" style="margin-left: auto;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    AI Vision OCR
                </span>
            </div>
            <div class="form-section-body">
                <div class="upload-zone" id="paste-zone" tabindex="0">
                    <div class="upload-zone-icon">
                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.8" style="margin: 0 auto 6px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    </div>
                    <div style="font-weight: 600; font-size: 13px; color: var(--text);">
                        Klik untuk memilih gambar, atau langsung <span style="color: var(--primary);">Ctrl + V</span> di sini
                    </div>
                    <div class="paste-zone-hint">
                        Bisa juga Drag & Drop gambar screenshot marketplace · Format PNG, JPG, WebP (Max 3 gambar)
                    </div>
                </div>

                {{-- Hidden Real File Input --}}
                <input type="file" id="file-input" name="images[]" multiple accept="image/*" style="display:none">

                {{-- Image Previews Container --}}
                <div class="image-previews" id="image-previews"></div>
                
                @error('images')<div class="form-error mt-8">{{ $message }}</div>@enderror
                @error('images.*')<div class="form-error mt-8">{{ $message }}</div>@enderror
                <div id="image-count-hint" class="form-hint mt-8">0 dari maksimal 3 gambar dipilih.</div>
            </div>
        </div>

        <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 16px;">
            <a href="{{ route('procurements.show', $procurement) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" id="btn-submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Simpan & Mulai Analisis AI
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const pasteZone   = document.getElementById('paste-zone');
const fileInput   = document.getElementById('file-input');
const previewWrap = document.getElementById('image-previews');
const form        = document.getElementById('candidate-form');
const btnSubmit   = document.getElementById('btn-submit');
const countHint   = document.getElementById('image-count-hint');

let selectedFiles = [];

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;

    countHint.textContent = `${selectedFiles.length} dari maksimal 3 gambar dipilih.`;
    if (selectedFiles.length === 0) {
        countHint.style.color = 'var(--muted)';
    } else {
        countHint.style.color = 'var(--primary)';
    }
}

function renderPreviews() {
    previewWrap.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
            const item = document.createElement('div');
            item.className = 'image-preview-item';
            item.innerHTML = `
                <img src="${e.target.result}" alt="screenshot ${index + 1}">
                <button type="button" class="remove-img" onclick="removeImage(${index})" title="Hapus gambar">✕</button>
            `;
            previewWrap.appendChild(item);
        };
        reader.readAsDataURL(file);
    });
    updateFileInput();
}

function addFiles(filesToAdd) {
    const remainingSlots = 3 - selectedFiles.length;
    if (remainingSlots <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Batas Maksimal',
            text: 'Maksimal hanya dapat mengunggah 3 gambar screenshot.',
            confirmButtonColor: '#15803D'
        });
        return;
    }

    const validNewFiles = Array.from(filesToAdd)
        .filter(f => f.type.startsWith('image/'))
        .slice(0, remainingSlots);

    if (validNewFiles.length === 0 && filesToAdd.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Format Tidak Sesuai',
            text: 'Harap hanya mengunggah file gambar (PNG, JPG, WebP).',
            confirmButtonColor: '#15803D'
        });
        return;
    }

    selectedFiles = selectedFiles.concat(validNewFiles);
    renderPreviews();
}

window.removeImage = function(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
};

// Click upload zone
pasteZone.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('change', () => {
    addFiles(fileInput.files);
});

// Drag & Drop
pasteZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    pasteZone.classList.add('drag-over');
});
pasteZone.addEventListener('dragleave', () => {
    pasteZone.classList.remove('drag-over');
});
pasteZone.addEventListener('drop', (e) => {
    e.preventDefault();
    pasteZone.classList.remove('drag-over');
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        addFiles(e.dataTransfer.files);
    }
});

// Clipboard Paste (Ctrl + V)
window.addEventListener('paste', (e) => {
    const items = (e.clipboardData || e.originalEvent.clipboardData).items;
    const pastedFiles = [];
    for (let item of items) {
        if (item.kind === 'file' && item.type.startsWith('image/')) {
            pastedFiles.push(item.getAsFile());
        }
    }
    if (pastedFiles.length > 0) {
        e.preventDefault();
        addFiles(pastedFiles);
    }
});

// Form submission validation & loading feedback
form.addEventListener('submit', (e) => {
    if (selectedFiles.length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Gambar Belum Dipilih',
            text: 'Harap unggah minimal 1 gambar screenshot iklan produk.',
            confirmButtonColor: '#15803D'
        });
        return false;
    }

    btnSubmit.disabled = true;
    btnSubmit.innerHTML = `<span class="spinner" style="margin-right:6px;"></span> Mengunggah & Memproses...`;
});
</script>
@endpush
