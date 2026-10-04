@extends('layouts.app')

@section('title', 'Edit Pengadaan')

@section('content')
<div class="page-header" style="align-items: center; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 14px;">
        <a href="{{ route('procurements.show', $procurement) }}" class="btn-back" title="Kembali ke Detail Pengadaan">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div>
            <h1 class="page-title">Edit Pengadaan</h1>
            <div class="page-header-sub">{{ $procurement->name }}</div>
        </div>
    </div>
</div>

<div style="max-width: 640px;">
    <form method="POST" action="{{ route('procurements.update', $procurement) }}">
        @csrf @method('PUT')

        <div class="form-section">
            <div class="form-section-header green">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Perbarui Informasi Pengadaan
            </div>
            <div class="form-section-body">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Pengadaan <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $procurement->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi & Catatan Kebutuhan</label>
                    <textarea id="description" name="description" class="form-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}">{{ old('description', $procurement->description) }}</textarea>
                    @error('description')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="status">Status Pengadaan</label>
                    <select id="status" name="status" class="form-select">
                        <option value="draft"     {{ $procurement->status === 'draft'     ? 'selected' : '' }}>Draft (Penyusunan Kriteria)</option>
                        <option value="active"    {{ $procurement->status === 'active'    ? 'selected' : '' }}>Aktif (Pengumpulan & Penilaian Kandidat)</option>
                        <option value="completed" {{ $procurement->status === 'completed' ? 'selected' : '' }}>Selesai (Keputusan Final Diambil)</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 8px; justify-content: flex-end; margin-top: 14px;">
            <a href="{{ route('procurements.show', $procurement) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
