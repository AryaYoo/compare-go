@extends('layouts.app')

@section('title', $procurement->name)

@section('content')
@php
    $totalWeight = $procurement->criteria->sum('weight');
    $statusMap   = [
        'draft'     => ['label'=>'Draft',    'class'=>'badge-gray'],
        'active'    => ['label'=>'Aktif',    'class'=>'badge-green'],
        'completed' => ['label'=>'Selesai',  'class'=>'badge-blue']
    ];
    $st = $statusMap[$procurement->status] ?? $statusMap['draft'];
@endphp

{{-- Page Header --}}
<div class="page-header">
    <div>
        <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">
            <a href="{{ route('procurements.index') }}" style="color: var(--muted); text-decoration: underline;">Pengadaan</a> &rsaquo; Detail Evaluasi
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h1 class="page-title">{{ $procurement->name }}</h1>
            <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
        </div>
        <div style="font-size: 12px; color: var(--muted); margin-top: 3px;">
            {{ $procurement->candidates->count() }} kandidat produk · Dibuat {{ $procurement->created_at->format('d M Y') }}
            @if($procurement->description)
                · <span title="{{ $procurement->description }}">{{ Str::limit($procurement->description, 80) }}</span>
            @endif
        </div>
    </div>
    <div class="page-actions">
        <a href="{{ route('procurements.edit', $procurement) }}" class="btn btn-secondary btn-sm" title="Edit Master Pengadaan">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
        </a>
        <a href="{{ route('procurements.candidates.create', $procurement) }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Kandidat
        </a>
    </div>
</div>

{{-- CRITERIA PANEL --}}
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="card-title">Kriteria Penilaian & Target Pengadaan</span>
            <span class="weight-total {{ $totalWeight == 100 ? 'ok' : 'not-ok' }}" id="weight-total-badge">
                Total Bobot: <span id="weight-total-value">{{ $totalWeight }}</span>%
            </span>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" id="btn-add-criterion">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Kriteria
        </button>
    </div>
    
    {{-- Total Weight Progress Bar --}}
    <div style="height: 3px; background: #E5E7EB; width: 100%;">
        <div id="weight-progress-bar" style="height: 100%; width: {{ min(100, $totalWeight) }}%; background: {{ $totalWeight == 100 ? 'var(--primary)' : 'var(--warning)' }}; transition: width 0.3s ease;"></div>
    </div>

    <div class="card-body" style="padding: 0;">

        {{-- Add/Edit Inline Form --}}
        <div id="criterion-form-wrap" style="display: none; padding: 14px 18px; border-bottom: 1px solid var(--border); background: var(--sidebar);">
            <div style="display: grid; grid-template-columns: 1fr 1fr 100px auto; gap: 10px; align-items: flex-end;">
                <div>
                    <label class="form-label">Nama Kriteria <span class="required">*</span></label>
                    <input type="text" id="cf-name" class="form-input" placeholder="cth: CPU / RAM / SSD / Layar">
                </div>
                <div>
                    <label class="form-label">Target / Spesifikasi Minimal <span class="required">*</span></label>
                    <input type="text" id="cf-target" class="form-input" placeholder="cth: Min. Intel Core i5 Gen 12 / 16GB">
                </div>
                <div>
                    <label class="form-label">Bobot (%) <span class="required">*</span></label>
                    <input type="number" id="cf-weight" class="form-input" placeholder="25" min="1" max="100">
                </div>
                <div style="display: flex; gap: 6px;">
                    <button type="button" class="btn btn-primary btn-sm" id="cf-save">Simpan</button>
                    <button type="button" class="btn btn-ghost btn-sm" id="cf-cancel">Batal</button>
                </div>
            </div>
            <div id="cf-error" style="color: var(--danger); font-size: 11.5px; margin-top: 6px; display: none;"></div>
            <input type="hidden" id="cf-editing-id">
        </div>

        {{-- Criteria List --}}
        <div id="criteria-list">
            @forelse($procurement->criteria as $criterion)
                <div class="criterion-row" data-id="{{ $criterion->id }}"
                     data-name="{{ $criterion->name }}" data-target="{{ $criterion->target }}" data-weight="{{ $criterion->weight }}"
                     style="display: flex; align-items: center; justify-content: space-between; padding: 10px 18px; border-bottom: 1px solid var(--border);">
                    <div style="flex: 1; min-width: 0; display: flex; align-items: center; gap: 10px;">
                        <span style="font-weight: 600; font-size: 13px; color: var(--text);">{{ $criterion->name }}</span>
                        <span style="color: var(--muted); font-size: 12px;">&rarr; Target: <strong style="color: var(--text);">{{ $criterion->target }}</strong></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="badge badge-gray" style="font-size: 11px;">{{ $criterion->weight }}%</span>
                        <button type="button" class="btn btn-ghost btn-sm btn-edit-criterion" style="padding: 4px 6px;" title="Edit Kriteria">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btn-del-criterion" style="padding: 4px 6px;" title="Hapus Kriteria">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding: 24px;" id="criteria-empty">
                    <div class="empty-state-desc">Belum ada kriteria target. Klik <strong>+ Tambah Kriteria</strong> untuk menyusun kriteria penilaian.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- TABS --}}
<div class="tabs">
    <div class="tab-item active" data-tab="candidates">
        Kandidat Iklan ({{ $procurement->candidates->count() }})
    </div>
    <div class="tab-item" data-tab="comparison">
        Matriks Komparasi & Rekomendasi AI
    </div>
</div>

{{-- TAB 1: CANDIDATES --}}
<div class="tab-panel active" id="tab-candidates">
    @if($procurement->candidates->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">📸</div>
                <div class="empty-state-title">Belum ada kandidat iklan produk</div>
                <div class="empty-state-desc">Unggah screenshot iklan marketplace (Shopee, Tokopedia, dll) untuk dianalisis oleh AI.</div>
                <div style="margin-top: 14px;">
                    <a href="{{ route('procurements.candidates.create', $procurement) }}" class="btn btn-primary btn-sm">
                        + Tambah Kandidat Sekarang
                    </a>
                </div>
            </div>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @foreach($procurement->candidates as $candidate)
                @php
                    $statusMap2 = [
                        'pending'    => ['label'=>'Menunggu',    'class'=>'badge-gray',   'icon'=>'⏳'],
                        'processing' => ['label'=>'Menganalisis','class'=>'badge-yellow', 'icon'=>'🔄'],
                        'done'       => ['label'=>'Selesai',     'class'=>'badge-green',  'icon'=>'✓'],
                        'failed'     => ['label'=>'Gagal',       'class'=>'badge-red',    'icon'=>'✕'],
                    ];
                    $cs = $statusMap2[$candidate->ai_status] ?? $statusMap2['pending'];
                    $score = $candidate->total_score;
                    $scoreClass = $score >= 70 ? '' : ($score >= 40 ? 'mid' : 'low');
                    $firstImg = count($candidate->images ?? []) ? $candidate->images[0] : null;
                @endphp
                <div class="list-item candidate-item" data-id="{{ $candidate->id }}"
                     data-status-url="{{ route('procurements.candidates.status', [$procurement, $candidate]) }}">
                    
                    {{-- Thumbnail --}}
                    @if($firstImg)
                        <div style="width: 52px; height: 52px; border-radius: 5px; overflow: hidden; border: 1px solid var(--border); flex-shrink: 0; background: #FAFAFA;">
                            <img src="{{ Storage::url($firstImg) }}" alt="{{ $candidate->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @else
                        <div style="width: 52px; height: 52px; border-radius: 5px; background: var(--sidebar); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 18px; flex-shrink: 0;">
                            🖼️
                        </div>
                    @endif

                    <div class="list-item-body">
                        <div class="list-item-title">
                            <a href="{{ route('procurements.candidates.show', [$procurement, $candidate]) }}" style="color: var(--text);" class="hover-underline">
                                {{ $candidate->name }}
                            </a>
                        </div>
                        <div class="list-item-meta">
                            <span class="badge {{ $cs['class'] }} candidate-status-badge" data-id="{{ $candidate->id }}">
                                {{ $cs['icon'] }} {{ $cs['label'] }}
                            </span>

                            @if($candidate->ai_status === 'done' && $score !== null)
                                <div class="score-bar" style="width: 140px;">
                                    <div class="score-bar-track">
                                        <div class="score-bar-fill {{ $scoreClass }}" style="width: {{ $score }}%;"></div>
                                    </div>
                                    <span class="score-value">{{ number_format($score, 1) }}</span>
                                </div>
                            @elseif($candidate->ai_status === 'processing')
                                <span class="spinner"></span>
                                <span class="text-muted fs-12">Vision AI OCR...</span>
                            @elseif($candidate->ai_status === 'failed')
                                <span style="font-size: 11.5px; color: var(--danger);">{{ Str::limit($candidate->ai_error, 60) }}</span>
                            @endif

                            <span>{{ count($candidate->images ?? []) }} gambar screenshot</span>
                            <span>{{ $candidate->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div class="list-item-actions">
                        <a href="{{ route('procurements.candidates.show', [$procurement, $candidate]) }}" class="btn btn-secondary btn-sm">
                            Detail AI
                        </a>
                        @if($candidate->ai_status === 'failed')
                            <button type="button" class="btn btn-secondary btn-sm btn-reanalyze"
                                    data-url="{{ route('procurements.candidates.reanalyze', [$procurement, $candidate]) }}"
                                    data-id="{{ $candidate->id }}" title="Ulangi Analisis">
                                ↺ Ulangi
                            </button>
                        @endif
                        <button type="button" class="btn btn-danger btn-sm" title="Hapus Kandidat" onclick="confirmDeleteCandidate('{{ $candidate->id }}', '{{ addslashes($candidate->name) }}')">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                        <form id="del-cand-form-{{ $candidate->id }}" method="POST" action="{{ route('procurements.candidates.destroy', [$procurement, $candidate]) }}" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- TAB 2: COMPARISON --}}
<div class="tab-panel" id="tab-comparison">
    @php
        $doneCandidates = $procurement->candidates->where('ai_status', 'done');
        $criteria       = $procurement->criteria;
    @endphp

    @if($doneCandidates->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <div class="empty-state-title">Belum ada data perbandingan</div>
                <div class="empty-state-desc">Tambahkan kandidat produk dan tunggu analisis AI selesai untuk melihat komparasi dan rekomendasi terbaik.</div>
            </div>
        </div>
    @else
        {{-- AI Recommendation Banner --}}
        @php $best = $doneCandidates->sortByDesc('total_score')->first(); @endphp
        @if($best)
            <div class="recommendation-banner">
                <div style="font-size: 26px; line-height: 1;">⭐</div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <span class="winner-pill">Rekomendasi AI Terbaik</span>
                        <span style="font-size: 14px; font-weight: 700; color: var(--text);">{{ $best->name }}</span>
                    </div>
                    <div style="font-size: 12.5px; color: var(--text); line-height: 1.4;">
                        Kandidat ini memperoleh skor kesesuaian tertinggi sebesar <strong>{{ number_format($best->total_score, 1) }} / 100</strong> berdasarkan pembobotan {{ $criteria->count() }} kriteria yang ditentukan.
                    </div>
                </div>
            </div>
        @endif

        {{-- Comparison Matrix Table --}}
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="min-width: 180px;">Kriteria / Target</th>
                        @foreach($doneCandidates->sortByDesc('total_score') as $c)
                            <th style="min-width: 160px; {{ $c->id === $best->id ? 'background: var(--primary-light); color: var(--primary);' : '' }}">
                                <div style="font-size: 12.5px; font-weight: 700;">{{ $c->name }}</div>
                                @if($c->id === $best->id)
                                    <div style="font-size: 10px; font-weight: 700; color: var(--primary); margin-top: 2px;">★ BEST VALUE</div>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteria as $criterion)
                        <tr>
                            <td>
                                <div style="font-weight: 600; font-size: 12.5px; color: var(--text);">{{ $criterion->name }}</div>
                                <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">Target: {{ $criterion->target }}</div>
                                <div style="font-size: 10.5px; color: var(--muted);">Bobot: {{ $criterion->weight }}%</div>
                            </td>
                            @foreach($doneCandidates->sortByDesc('total_score') as $c)
                                @php
                                    $s = $c->scores->firstWhere('criteria_id', $criterion->id);
                                    $sc = $s ? $s->score : null;
                                    $fillClass = $sc === null ? '' : ($sc >= 70 ? '' : ($sc >= 40 ? 'mid' : 'low'));
                                @endphp
                                <td style="{{ $c->id === $best->id ? 'background: #FAFEFC;' : '' }}">
                                    @if($s)
                                        <div style="font-size: 12.5px; font-weight: 600; color: var(--text); margin-bottom: 4px;">
                                            {{ $s->extracted_value ?: '—' }}
                                        </div>
                                        <div class="score-bar" style="margin-bottom: 4px;">
                                            <div class="score-bar-track">
                                                <div class="score-bar-fill {{ $fillClass }}" style="width: {{ $sc }}%;"></div>
                                            </div>
                                            <span class="score-value">{{ number_format($sc, 0) }}</span>
                                        </div>
                                        @if($s->reasoning)
                                            <div style="font-size: 11px; color: var(--muted); line-height: 1.3;">{{ Str::limit($s->reasoning, 85) }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted fs-12">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: var(--sidebar); font-weight: 700; border-top: 2px solid var(--border);">
                        <td style="font-size: 12px; text-transform: uppercase;">Total Skor Kesesuaian</td>
                        @foreach($doneCandidates->sortByDesc('total_score') as $c)
                            <td style="{{ $c->id === $best->id ? 'background: var(--primary-light); color: var(--primary); font-size: 15px; font-weight: 700;' : 'font-size: 14px;' }}">
                                {{ number_format($c->total_score, 1) }} <span style="font-size: 11px; font-weight: 400; color: var(--muted);">/ 100</span>
                            </td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const procurementId = {{ $procurement->id }};

// Tab switching
document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
    });
});

// Inline Criteria Logic
const formWrap  = document.getElementById('criterion-form-wrap');
const cfName    = document.getElementById('cf-name');
const cfTarget  = document.getElementById('cf-target');
const cfWeight  = document.getElementById('cf-weight');
const cfError   = document.getElementById('cf-error');
const cfId      = document.getElementById('cf-editing-id');
const cfSave    = document.getElementById('cf-save');
const cfCancel  = document.getElementById('cf-cancel');
const criteriaList = document.getElementById('criteria-list');

function showForm(data = null) {
    cfId.value     = data ? data.id : '';
    cfName.value   = data ? data.name : '';
    cfTarget.value = data ? data.target : '';
    cfWeight.value = data ? data.weight : '';
    cfError.style.display = 'none';
    formWrap.style.display = 'block';
    cfName.focus();
}

function hideForm() {
    formWrap.style.display = 'none';
    cfId.value = cfName.value = cfTarget.value = cfWeight.value = '';
}

function updateWeightBadge(total) {
    document.getElementById('weight-total-value').textContent = total;
    const badge = document.getElementById('weight-total-badge');
    badge.className = 'weight-total ' + (total == 100 ? 'ok' : 'not-ok');
    
    const bar = document.getElementById('weight-progress-bar');
    if (bar) {
        bar.style.width = Math.min(100, total) + '%';
        bar.style.background = total == 100 ? 'var(--primary)' : 'var(--warning)';
    }
}

function renderCriterionRow(c) {
    const div = document.createElement('div');
    div.className = 'criterion-row';
    div.dataset.id     = c.id;
    div.dataset.name   = c.name;
    div.dataset.target = c.target;
    div.dataset.weight = c.weight;
    div.style = 'display: flex; align-items: center; justify-content: space-between; padding: 10px 18px; border-bottom: 1px solid var(--border);';
    div.innerHTML = `
        <div style="flex: 1; min-width: 0; display: flex; align-items: center; gap: 10px;">
            <span style="font-weight: 600; font-size: 13px; color: var(--text);">${c.name}</span>
            <span style="color: var(--muted); font-size: 12px;">&rarr; Target: <strong style="color: var(--text);">${c.target}</strong></span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="badge badge-gray" style="font-size: 11px;">${c.weight}%</span>
            <button type="button" class="btn btn-ghost btn-sm btn-edit-criterion" style="padding: 4px 6px;" title="Edit Kriteria">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button type="button" class="btn btn-danger btn-sm btn-del-criterion" style="padding: 4px 6px;" title="Hapus Kriteria">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
        </div>
    `;
    return div;
}

document.getElementById('btn-add-criterion')?.addEventListener('click', () => showForm());
cfCancel?.addEventListener('click', hideForm);

cfSave?.addEventListener('click', async () => {
    const name   = cfName.value.trim();
    const target = cfTarget.value.trim();
    const weight = parseInt(cfWeight.value);
    if (!name || !target || !weight) {
        cfError.textContent = 'Semua field kriteria wajib diisi.';
        cfError.style.display = 'block';
        return;
    }

    const editingId = cfId.value;
    const url = editingId
        ? `/procurements/${procurementId}/criteria/${editingId}`
        : `/procurements/${procurementId}/criteria`;
    const method = editingId ? 'PUT' : 'POST';

    cfSave.disabled = true;
    try {
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ name, target, weight }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || 'Gagal menyimpan.');

        if (editingId) {
            const row = criteriaList.querySelector(`.criterion-row[data-id="${editingId}"]`);
            if (row) {
                const newRow = renderCriterionRow(data.criterion);
                criteriaList.replaceChild(newRow, row);
            }
        } else {
            const empty = document.getElementById('criteria-empty');
            if (empty) empty.remove();
            criteriaList.appendChild(renderCriterionRow(data.criterion));
        }

        updateWeightBadge(data.total_weight);
        hideForm();

        Swal.fire({
            icon: 'success',
            title: 'Kriteria Disimpan',
            timer: 1500,
            showConfirmButton: false
        });
    } catch (e) {
        cfError.textContent = e.message;
        cfError.style.display = 'block';
    } finally {
        cfSave.disabled = false;
    }
});

criteriaList?.addEventListener('click', async (e) => {
    const editBtn = e.target.closest('.btn-edit-criterion');
    const delBtn  = e.target.closest('.btn-del-criterion');

    if (editBtn) {
        const row = editBtn.closest('.criterion-row');
        showForm({ id: row.dataset.id, name: row.dataset.name, target: row.dataset.target, weight: row.dataset.weight });
    }

    if (delBtn) {
        const row = delBtn.closest('.criterion-row');
        const id  = row.dataset.id;
        const name = row.dataset.name;

        Swal.fire({
            title: 'Hapus Kriteria?',
            text: `Kriteria "${name}" akan dihapus dari pengadaan ini.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/procurements/${procurementId}/criteria/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF },
                    });
                    const data = await res.json();
                    row.remove();
                    updateWeightBadge(data.total_weight);
                    if (!criteriaList.querySelector('.criterion-row')) {
                        criteriaList.innerHTML = `<div class="empty-state" style="padding:24px;" id="criteria-empty"><div class="empty-state-desc">Belum ada kriteria target. Klik <strong>+ Tambah Kriteria</strong> untuk menyusun kriteria penilaian.</div></div>`;
                    }
                } catch {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus kriteria.' });
                }
            }
        });
    }
});

function confirmDeleteCandidate(id, name) {
    Swal.fire({
        title: 'Hapus Kandidat?',
        text: `Kandidat "${name}" beserta screenshot dan hasil analisisnya akan dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`del-cand-form-${id}`).submit();
        }
    });
}

// Background auto-polling for candidate analysis updates
const pendingItems = document.querySelectorAll('.candidate-item[data-status-url]');
const pollingItems = Array.from(pendingItems).filter(el => {
    const badge = el.querySelector('.candidate-status-badge');
    return badge && (badge.textContent.includes('Menunggu') || badge.textContent.includes('Menganalisis'));
});

if (pollingItems.length > 0) {
    const pollInterval = setInterval(async () => {
        let stillPending = false;
        for (const item of pollingItems) {
            const url = item.dataset.statusUrl;
            try {
                const res  = await fetch(url);
                const data = await res.json();
                if (data.ai_status === 'done' || data.ai_status === 'failed') {
                    clearInterval(pollInterval);
                    window.location.reload();
                    return;
                }
                if (data.ai_status === 'pending' || data.ai_status === 'processing') {
                    stillPending = true;
                }
            } catch {}
        }
        if (!stillPending) clearInterval(pollInterval);
    }, 3500);
}

// Re-analyze click
document.querySelectorAll('.btn-reanalyze').forEach(btn => {
    btn.addEventListener('click', async () => {
        btn.disabled = true;
        btn.textContent = '...';
        try {
            await fetch(btn.dataset.url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } });
            window.location.reload();
        } catch {
            btn.disabled = false;
            btn.textContent = '↺ Ulangi';
        }
    });
});
</script>
@endpush
