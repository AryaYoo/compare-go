@extends('layouts.app')

@section('title', 'Daftar Pengadaan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Pengadaan Barang</h1>
        <div class="page-header-sub">{{ $procurements->count() }} pengadaan tercatat dalam sistem</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('procurements.create') }}" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Pengadaan
        </a>
    </div>
</div>

@php
    $totalProcurements = $procurements->count();
    $activeProcurements = $procurements->where('status', 'active')->count();
    $totalCandidates = $procurements->sum('candidates_count');
    $completedProcurements = $procurements->where('status', 'completed')->count();
@endphp

{{-- Stat Cards --}}
<div class="stat-cards">
    <div class="stat-card stat-card-total">
        <div class="stat-card-label">Total Pengadaan</div>
        <div class="stat-card-value">{{ $totalProcurements }}</div>
    </div>
    <div class="stat-card stat-card-active">
        <div class="stat-card-label">Pengadaan Aktif</div>
        <div class="stat-card-value">{{ $activeProcurements }}</div>
    </div>
    <div class="stat-card stat-card-candidates">
        <div class="stat-card-label">Total Kandidat</div>
        <div class="stat-card-value">{{ $totalCandidates }}</div>
    </div>
    <div class="stat-card stat-card-ready">
        <div class="stat-card-label">Pengadaan Selesai</div>
        <div class="stat-card-value">{{ $completedProcurements }}</div>
    </div>
</div>

{{-- Action & Search Bar --}}
<div class="procurements-action-bar">
    <div class="search-input-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="procurement-search" class="search-input" placeholder="Cari nama pengadaan atau deskripsi..." onkeyup="filterTable()">
        <span id="search-clear" class="search-clear-btn" style="display:none;" onclick="clearSearch()">✕</span>
    </div>

    <div style="display: flex; gap: 6px;">
        <button type="button" class="btn btn-secondary btn-sm filter-pill active" onclick="setFilterStatus('all', this)">Semua</button>
        <button type="button" class="btn btn-secondary btn-sm filter-pill" onclick="setFilterStatus('draft', this)">Draft</button>
        <button type="button" class="btn btn-secondary btn-sm filter-pill" onclick="setFilterStatus('active', this)">Aktif</button>
        <button type="button" class="btn btn-secondary btn-sm filter-pill" onclick="setFilterStatus('completed', this)">Selesai</button>
    </div>
</div>

{{-- Table --}}
<div class="card">
    @if($procurements->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <div class="empty-state-title">Belum ada pengadaan barang</div>
            <div class="empty-state-desc">Mulai dengan membuat master pengadaan baru untuk membandingkan iklan produk secara otomatis.</div>
            <div style="margin-top: 14px;">
                <a href="{{ route('procurements.create') }}" class="btn btn-primary btn-sm">
                    + Buat Pengadaan Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="table-wrap" style="border:none;">
            <table class="data-table" id="procurements-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Nama Pengadaan</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 18%;">Kriteria & Bobot</th>
                        <th style="width: 12%;">Kandidat</th>
                        <th style="width: 13%;">Dibuat</th>
                        <th style="width: 10%; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($procurements as $procurement)
                        @php
                            $totalWeight = $procurement->criteria->sum('weight');
                            $statusMap = [
                                'draft'     => ['label'=>'Draft',    'class'=>'badge-gray'],
                                'active'    => ['label'=>'Aktif',    'class'=>'badge-green'],
                                'completed' => ['label'=>'Selesai',  'class'=>'badge-blue']
                            ];
                            $st = $statusMap[$procurement->status] ?? $statusMap['draft'];
                        @endphp
                        <tr class="procurement-row" data-status="{{ $procurement->status }}" data-name="{{ strtolower($procurement->name . ' ' . $procurement->description) }}">
                            <td>
                                <div>
                                    <a href="{{ route('procurements.show', $procurement) }}" style="font-weight: 600; color: var(--text);" class="hover-underline">
                                        {{ $procurement->name }}
                                    </a>
                                </div>
                                @if($procurement->description)
                                    <div class="text-muted fs-12 mt-4" style="line-height: 1.4;">
                                        {{ Str::limit($procurement->description, 85) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $st['class'] }}">{{ $st['label'] }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span style="font-weight: 500;">{{ $procurement->criteria->count() }} Kriteria</span>
                                    @if($totalWeight > 0)
                                        <span class="weight-total {{ $totalWeight == 100 ? 'ok' : 'not-ok' }}" style="font-size: 10.5px;">
                                            {{ $totalWeight }}%
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 500;">{{ $procurement->candidates_count }}</span>
                                <span class="text-muted fs-12">produk</span>
                            </td>
                            <td>
                                <span class="text-muted fs-12" title="{{ $procurement->created_at->format('d M Y H:i') }}">
                                    {{ $procurement->created_at->diffForHumans() }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                    <a href="{{ route('procurements.show', $procurement) }}" class="btn btn-secondary btn-sm" title="Lihat Evaluasi">
                                        Detail
                                    </a>
                                    <a href="{{ route('procurements.edit', $procurement) }}" class="btn btn-ghost btn-sm" title="Edit Pengadaan">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" title="Hapus Pengadaan" onclick="confirmDelete('{{ $procurement->id }}', '{{ addslashes($procurement->name) }}')">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $procurement->id }}" method="POST" action="{{ route('procurements.destroy', $procurement) }}" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
let currentStatus = 'all';

function filterTable() {
    const searchVal = document.getElementById('procurement-search').value.toLowerCase().trim();
    const clearBtn = document.getElementById('search-clear');
    clearBtn.style.display = searchVal ? 'block' : 'none';

    const rows = document.querySelectorAll('.procurement-row');
    rows.forEach(row => {
        const text = row.getAttribute('data-name');
        const status = row.getAttribute('data-status');
        const matchesSearch = text.includes(searchVal);
        const matchesStatus = (currentStatus === 'all' || status === currentStatus);

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearSearch() {
    document.getElementById('procurement-search').value = '';
    filterTable();
}

function setFilterStatus(status, btn) {
    currentStatus = status;
    document.querySelectorAll('.filter-pill').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    filterTable();
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Pengadaan?',
        text: `Pengadaan "${name}" beserta semua kandidat dan skor AI akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}
</script>
@endpush
