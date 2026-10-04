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
<div class="page-header" style="align-items: center; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; gap: 14px;">
        <a href="{{ route('procurements.index') }}" class="btn-back" title="Kembali ke Daftar Pengadaan">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div>
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
    </div>
    <div class="page-actions">
        <a href="{{ route('procurements.edit', $procurement) }}" class="btn btn-secondary btn-sm" title="Edit Master Pengadaan">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
        </a>
    </div>
</div>

{{-- TABS --}}
<div class="tabs">
    <div class="tab-item active" data-tab="criteria">
        Kriteria (<span id="criteria-tab-count">{{ $procurement->criteria->count() }}</span>)
    </div>
    <div class="tab-item" data-tab="candidates">
        Kandidat Iklan ({{ $procurement->candidates->count() }})
    </div>
    <div class="tab-item" data-tab="comparison">
        Hasil Komparasi
    </div>
</div>

{{-- TAB 1: CRITERIA --}}
<div class="tab-panel active" id="tab-criteria">
    <div class="card">
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
</div>

{{-- TAB 2: CANDIDATES --}}
<div class="tab-panel" id="tab-candidates">
    {{-- Square Add Candidate Button at the top (centered) --}}
    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
        <a href="{{ route('procurements.candidates.create', $procurement) }}" 
           class="btn-add-square" 
           title="Tambah Kandidat">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </a>
    </div>

    @if($procurement->candidates->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">📸</div>
                <div class="empty-state-title">Belum ada kandidat iklan produk</div>
                <div class="empty-state-desc">Unggah screenshot iklan marketplace (Shopee, Tokopedia, dll) untuk dianalisis oleh AI.</div>
            </div>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @foreach($procurement->candidates->sortByDesc('id') as $candidate)
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
                     data-status-url="{{ route('procurements.candidates.status', [$procurement, $candidate]) }}"
                     onclick="window.location='{{ route('procurements.candidates.show', [$procurement, $candidate]) }}'">
                    
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
                            {{ $candidate->name }}
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
                        @if($candidate->ai_status === 'failed')
                            <button type="button" class="btn btn-secondary btn-sm btn-reanalyze"
                                    data-url="{{ route('procurements.candidates.reanalyze', [$procurement, $candidate]) }}"
                                    data-id="{{ $candidate->id }}" title="Ulangi Analisis" onclick="event.stopPropagation()">
                                ↺ Ulangi
                            </button>
                        @endif
                        <button type="button" class="btn btn-danger btn-sm" title="Hapus Kandidat" onclick="event.stopPropagation(); confirmDeleteCandidate('{{ $candidate->id }}', '{{ addslashes($candidate->name) }}')">
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

{{-- TAB 3: HASIL KOMPARASI --}}
<div class="tab-panel" id="tab-comparison">
    @php
        $topCandidates = $procurement->candidates
            ->where('ai_status', 'done')
            ->sortByDesc('total_score')
            ->take(3);

        if ($topCandidates->isEmpty()) {
            $topCandidates = $procurement->candidates
                ->whereNotNull('total_score')
                ->sortByDesc('total_score')
                ->take(3);
        }
    @endphp

    @if($topCandidates->isEmpty())
        <div class="card" style="padding: 36px 20px; text-align: center; border: 1px solid var(--border); border-radius: 8px; background: #FFFFFF;">
            <div style="font-size: 13.5px; font-weight: 600; color: var(--text); margin-bottom: 4px;">Belum Ada Hasil Komparasi</div>
            <div style="font-size: 12px; color: var(--muted);">Tambahkan kandidat produk pada tab Kandidat Iklan untuk melihat peringkat kandidat terbaik.</div>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($topCandidates as $index => $candidate)
                @php
                    $firstImg = count($candidate->images ?? []) ? $candidate->images[0] : null;
                    $score = $candidate->total_score;
                    $scoreClass = $score >= 70 ? '' : ($score >= 40 ? 'mid' : 'low');
                    $rank = $index + 1;
                @endphp
                <a href="{{ route('procurements.candidates.show', [$procurement, $candidate]) }}" 
                   class="top-candidate-card">
                    <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0;">
                        {{-- Rank Badge --}}
                        <div class="top-candidate-rank rank-{{ $rank }}">
                            #{{ $rank }}
                        </div>

                        {{-- Thumbnail --}}
                        @if($firstImg)
                            <div class="top-candidate-thumb">
                                <img src="{{ Storage::url($firstImg) }}" alt="{{ $candidate->name }}">
                            </div>
                        @else
                            <div class="top-candidate-thumb placeholder">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif

                        {{-- Info --}}
                        <div style="flex: 1; min-width: 0;">
                            <div class="top-candidate-name">
                                {{ $candidate->name }}
                            </div>
                            <div class="top-candidate-meta">
                                {{ count($candidate->images ?? []) }} gambar · {{ $candidate->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    {{-- Score & Arrow --}}
                    <div style="display: flex; align-items: center; gap: 18px; flex-shrink: 0;">
                        @if($score !== null)
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="score-bar" style="width: 120px;">
                                    <div class="score-bar-track">
                                        <div class="score-bar-fill {{ $scoreClass }}" style="width: {{ $score }}%;"></div>
                                    </div>
                                </div>
                                <div class="top-candidate-score">
                                    {{ number_format($score, 1) }} <span style="font-size: 11px; font-weight: 400; color: var(--muted);">/ 100</span>
                                </div>
                            </div>
                        @endif

                        <div class="top-candidate-arrow">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
.btn-add-square {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: #0D9488;
    color: #FFFFFF !important;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all 0.15s ease-in-out;
    text-decoration: none;
    flex-shrink: 0;
}

.btn-add-square:hover {
    background-color: #0F766E;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);
}

.btn-add-square:active {
    transform: scale(0.96);
}

.candidate-item {
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.candidate-item:hover {
    border-color: #CBD5E1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    transform: translateY(-1px);
}

.top-candidate-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    background: #FFFFFF;
    border: 1px solid var(--border);
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.15s ease-in-out;
}

.top-candidate-card:hover {
    border-color: #0D9488;
    background-color: #F8FAFC;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.06);
}

.top-candidate-rank {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12.5px;
    font-weight: 700;
    flex-shrink: 0;
}

.top-candidate-rank.rank-1 {
    background: #F0FDFA;
    color: #0D9488;
    border: 1px solid #99F6E4;
}

.top-candidate-rank.rank-2 {
    background: #F1F5F9;
    color: #475569;
    border: 1px solid #E2E8F0;
}

.top-candidate-rank.rank-3 {
    background: #F8FAFC;
    color: #64748B;
    border: 1px solid #E2E8F0;
}

.top-candidate-thumb {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid var(--border);
    flex-shrink: 0;
    background: #FAFAFA;
}

.top-candidate-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.top-candidate-thumb.placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
}

.top-candidate-name {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.top-candidate-meta {
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 3px;
}

.top-candidate-score {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
    min-width: 65px;
    text-align: right;
}

.top-candidate-arrow {
    color: #9CA3AF;
    display: flex;
    align-items: center;
    transition: transform 0.15s ease, color 0.15s ease;
}

.top-candidate-card:hover .top-candidate-arrow {
    color: #0D9488;
    transform: translateX(2px);
}
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const procurementId = {{ $procurement->id }};

// Tab switching with hash persistence
document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
        if (history.replaceState) {
            history.replaceState(null, null, '#' + tab.dataset.tab);
        }
    });
});

// Restore active tab from URL hash if provided
const currentTabHash = window.location.hash.replace('#', '');
if (currentTabHash) {
    const targetTabEl = document.querySelector(`.tab-item[data-tab="${currentTabHash}"]`);
    if (targetTabEl) {
        targetTabEl.click();
    }
}

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
        const tabCountEl = document.getElementById('criteria-tab-count');
        if (tabCountEl) {
            tabCountEl.textContent = criteriaList.querySelectorAll('.criterion-row').length;
        }
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
                    const tabCountEl = document.getElementById('criteria-tab-count');
                    if (tabCountEl) {
                        tabCountEl.textContent = criteriaList.querySelectorAll('.criterion-row').length;
                    }
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
