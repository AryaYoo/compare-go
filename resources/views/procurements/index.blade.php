@extends('layouts.app')

@section('title', 'Daftar Pengadaan')

@section('content')
<div class="page-header" style="margin-bottom: 14px;">
    <div>
        <h1 class="page-title">Pengadaan Barang</h1>
        <div class="page-header-sub">{{ $procurements->count() }} pengadaan tercatat dalam sistem</div>
    </div>
</div>

{{-- Action & Search Bar --}}
<div class="procurements-action-bar" style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
    {{-- Button Buat Pengadaan di sebelah kiri search bar dengan tinggi 36px yang sama persis --}}
    <a href="{{ route('procurements.create') }}" class="btn-create-procurement">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Buat Pengadaan</span>
    </a>

    {{-- Search Bar --}}
    <div class="search-input-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="procurement-search" class="search-input" placeholder="Cari nama pengadaan atau deskripsi..." onkeyup="filterTable()">
        <span id="search-clear" class="search-clear-btn" style="display:none;" onclick="clearSearch()">✕</span>
    </div>
</div>

{{-- Card List --}}
@if($procurements->isEmpty())
    <div class="card">
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
    </div>
@else
    <div class="procurement-card-list" id="procurement-card-list">
        @foreach($procurements as $procurement)
            @php
                $totalWeight = $procurement->criteria->sum('weight');
                $code = 'PGD-' . str_pad($procurement->id, 3, '0', STR_PAD_LEFT);
            @endphp
            <div class="procurement-card" 
                 data-status="{{ $procurement->status }}" 
                 data-name="{{ strtolower($procurement->name . ' ' . $procurement->description . ' ' . $code) }}"
                 onclick="window.location.href='{{ route('procurements.show', $procurement) }}'"
                 title="Klik untuk melihat detail & analisis">
                
                {{-- Top Line: Code, Badges, and Action buttons on right --}}
                <div class="procurement-card-header">
                    <div class="procurement-card-title-wrap">
                        <span class="procurement-card-code">{{ $code }}</span>
                        
                        @if($procurement->status === 'active')
                            <span class="proc-pill pill-blue">Aktif</span>
                        @elseif($procurement->status === 'completed')
                            <span class="proc-pill pill-green">Selesai</span>
                        @else
                            <span class="proc-pill pill-gray">Draft</span>
                        @endif

                        <span class="proc-pill pill-purple">{{ $procurement->candidates_count }} Kandidat</span>
                        <span class="proc-pill pill-teal">{{ $procurement->criteria->count() }} Kriteria</span>
                    </div>

                    {{-- Edit & Delete action icons (Detail button is removed, card is clickable) --}}
                    <div class="procurement-card-actions" onclick="event.stopPropagation()">
                        <a href="{{ route('procurements.edit', $procurement) }}" 
                           class="card-action-btn action-edit" 
                           title="Edit Pengadaan">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </a>
                        <button type="button" 
                                class="card-action-btn action-delete" 
                                title="Hapus Pengadaan"
                                onclick="confirmDelete('{{ $procurement->id }}', '{{ addslashes($procurement->name) }}')">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>

                    <form id="delete-form-{{ $procurement->id }}" method="POST" action="{{ route('procurements.destroy', $procurement) }}" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                </div>

                {{-- Middle Line: Title and Description --}}
                <div class="procurement-card-body">
                    <span class="procurement-main-title">{{ $procurement->name }}</span>
                    @if($procurement->description)
                        <span class="procurement-main-desc">&mdash; {{ Str::limit($procurement->description, 110) }}</span>
                    @endif
                </div>

                {{-- Bottom Line: PIC, Created Date --}}
                <div class="procurement-card-footer">
                    <span>PIC: <strong>{{ auth()->user()->name ?? 'Staff IT' }}</strong></span>
                    <span class="meta-dot">|</span>
                    <span>Dibuat: {{ $procurement->created_at->format('d/m/Y H:i') }}</span>
                    @if($totalWeight > 0)
                        <span class="meta-dot">|</span>
                        <span>Bobot Kriteria: {{ $totalWeight }}%</span>
                    @endif
                </div>

            </div>
        @endforeach
    </div>

    {{-- Empty search result state --}}
    <div id="no-search-results" class="card" style="display:none; text-align:center; padding: 36px 20px;">
        <div style="font-size: 26px; margin-bottom: 6px;">🔍</div>
        <div style="font-weight: 600; color: #1E293B; margin-bottom: 4px;">Tidak ada pengadaan ditemukan</div>
        <div style="font-size: 13px; color: #64748B;">Coba sesuaikan kata kunci pencarian atau filter status Anda.</div>
    </div>
@endif
@endsection

@push('styles')
<style>
.procurement-card-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.procurement-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-left: 4px solid #0D9488;
    border-radius: 8px;
    padding: 14px 18px;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 7px;
    user-select: none;
}

.procurement-card:hover {
    border-color: #CBD5E1;
    border-left-color: #0F766E;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
}

.procurement-card[data-status="draft"] {
    border-left-color: #94A3B8;
}

.procurement-card[data-status="active"] {
    border-left-color: #0D9488;
}

.procurement-card[data-status="completed"] {
    border-left-color: #16A34A;
}

.procurement-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.procurement-card-title-wrap {
    display: flex;
    align-items: center;
    gap: 9px;
    flex-wrap: wrap;
}

.procurement-card-code {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-weight: 700;
    font-size: 13.5px;
    color: #0D9488;
    letter-spacing: 0.3px;
}

.proc-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 500;
    line-height: 1.4;
}

.pill-blue {
    background-color: #F0FDFA;
    color: #0F766E;
}

.pill-green {
    background-color: #F0FDF4;
    color: #15803D;
}

.pill-purple {
    background-color: #F5F3FF;
    color: #6D28D9;
}

.pill-gray {
    background-color: #F1F5F9;
    color: #475569;
}

.pill-teal {
    background-color: #F0FDFA;
    color: #0F766E;
}

.procurement-card-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-left: auto;
}

.card-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    color: #94A3B8;
}

.card-action-btn.action-edit:hover {
    background: #F1F5F9;
    color: #0D9488;
    border-color: #E2E8F0;
}

.card-action-btn.action-delete:hover {
    background: #FEF2F2;
    color: #DC2626;
    border-color: #FECACA;
}

.procurement-card-body {
    font-size: 13px;
    color: #475569;
    line-height: 1.45;
}

.procurement-main-title {
    font-weight: 600;
    color: #0F172A;
    font-size: 13.5px;
}

.procurement-main-desc {
    color: #64748B;
    font-size: 13px;
}

.procurement-card-footer {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    color: #64748B;
    margin-top: 2px;
}

.btn-create-procurement {
    height: 36px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background-color: #0D9488;
    color: #FFFFFF !important;
    font-size: 13px;
    font-weight: 500;
    padding: 0 16px;
    border-radius: 6px;
    text-decoration: none;
    box-sizing: border-box;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all 0.15s ease-in-out;
    border: none;
    flex-shrink: 0;
}

.btn-create-procurement:hover {
    background-color: #0F766E;
    transform: translateY(-0.5px);
    box-shadow: 0 3px 8px rgba(13, 148, 136, 0.25);
}

.btn-create-procurement:active {
    transform: scale(0.98);
}

.search-input-wrap .search-input {
    height: 36px;
    box-sizing: border-box;
    border-radius: 6px;
}

.meta-dot {
    color: #CBD5E1;
}
</style>
@endpush

@push('scripts')
<script>
function filterTable() {
    const searchVal = document.getElementById('procurement-search').value.toLowerCase().trim();
    const clearBtn = document.getElementById('search-clear');
    clearBtn.style.display = searchVal ? 'block' : 'none';

    const cards = document.querySelectorAll('.procurement-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const text = card.getAttribute('data-name');
        const matchesSearch = text.includes(searchVal);

        if (matchesSearch) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const noResults = document.getElementById('no-search-results');
    if (noResults) {
        noResults.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
    }
}

function clearSearch() {
    document.getElementById('procurement-search').value = '';
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
