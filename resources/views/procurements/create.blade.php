@extends('layouts.app')

@section('title', 'Buat Pengadaan')

@section('content')
<div class="page-header">
    <div style="display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('procurements.index') }}" class="btn btn-ghost btn-sm" style="padding: 6px; color: var(--muted);" title="Kembali ke Daftar Pengadaan">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="page-title">Buat Pengadaan Baru</h1>
            <div class="page-header-sub">Definisikan master data pengadaan untuk mulai membandingkan kandidat iklan produk.</div>
        </div>
    </div>
</div>

<div style="max-width: 640px;">
    <form method="POST" action="{{ route('procurements.store') }}">
        @csrf

        <div class="form-section">
            <div class="form-section-header green">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                Informasi Master Pengadaan
            </div>
            <div class="form-section-body">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Pengadaan <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" placeholder="Contoh: Pengadaan Laptop Finance Q4 2026" autofocus required>
                    <div class="form-hint">Gunakan nama yang spesifik untuk mempermudah identifikasi kebutuhan.</div>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="description">Deskripsi & Catatan Kebutuhan</label>
                    <textarea id="description" name="description" class="form-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
                              placeholder="Contoh: Target kebutuhan laptop untuk tim finance dan accounting. Diutamakan RAM minimal 16GB, bobot ringan, dan garansi resmi.">{{ old('description') }}</textarea>
                    <div class="form-hint">Opsional: Informasi konteks pengguna, anggaran, atau catatan penting lainnya.</div>
                    @error('description')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 14px;">
            <a href="{{ route('procurements.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                Simpan & Lanjut ke Kriteria →
            </button>
        </div>
    </form>
</div>
@endsection
