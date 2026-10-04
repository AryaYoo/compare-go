@extends('layouts.app')

@section('title', $candidate->name)

@section('content')
@php
    $statusMap = [
        'pending'    => ['label'=>'Menunggu Antrean', 'class'=>'badge-gray',   'icon'=>'⏳'],
        'processing' => ['label'=>'AI Menganalisis',  'class'=>'badge-yellow', 'icon'=>'🔄'],
        'done'       => ['label'=>'Analisis Selesai', 'class'=>'badge-green',  'icon'=>'✓'],
        'failed'     => ['label'=>'Analisis Gagal',   'class'=>'badge-red',    'icon'=>'✕'],
    ];
    $cs = $statusMap[$candidate->ai_status] ?? $statusMap['pending'];
@endphp

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('procurements.show', $procurement) }}" class="btn btn-ghost btn-sm" style="padding: 6px; color: var(--muted);" title="Kembali ke Pengadaan">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div style="font-size: 11.5px; color: var(--muted); margin-bottom: 2px;">
                Pengadaan &rsaquo; {{ Str::limit($procurement->name, 40) }} &rsaquo; Kandidat
            </div>
            <h1 class="page-title">{{ $candidate->name }}</h1>
        </div>
    </div>
    <div class="page-actions">
        <span class="badge {{ $cs['class'] }}">
            {{ $cs['label'] }}
        </span>
        @if($candidate->ai_status === 'failed')
            <button class="btn btn-primary btn-sm" id="btn-reanalyze"
                    data-url="{{ route('procurements.candidates.reanalyze', [$procurement, $candidate]) }}">
                ↺ Ulangi Analisis AI
            </button>
        @endif
        <a href="{{ route('procurements.show', $procurement) }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; align-items: start;">

    {{-- Left Column: Images Preview --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Screenshot Iklan Marketplace</span>
            <span class="text-muted fs-12">{{ count($candidate->images ?? []) }} Gambar</span>
        </div>
        <div class="card-body">
            @php $images = $candidate->images ?? []; @endphp
            @if(count($images))
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($images as $img)
                        <div style="border: 1px solid var(--border); border-radius: 6px; overflow: hidden; background: #FAFAFA;">
                            <a href="{{ Storage::url($img) }}" target="_blank" title="Klik untuk memperbesar">
                                <img src="{{ Storage::url($img) }}" alt="Screenshot {{ $candidate->name }}"
                                     style="width: 100%; display: block; object-fit: contain; max-height: 480px;">
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🖼️</div>
                    <div class="empty-state-desc">Tidak ada gambar tersimpan.</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column: AI Analysis Result --}}
    <div>
        @if($candidate->ai_status === 'pending' || $candidate->ai_status === 'processing')
            <div class="card">
                <div class="card-body" style="text-align: center; padding: 48px 24px;">
                    <div class="spinner" style="width: 28px; height: 28px; margin: 0 auto 14px; border-width: 3px;"></div>
                    <div style="font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 4px;">
                        {{ $candidate->ai_status === 'pending' ? 'Menunggu antrean AI...' : 'AI Gemini Vision sedang menganalisis spesifikasi...' }}
                    </div>
                    <div style="font-size: 12px; color: var(--muted); max-width: 340px; margin: 0 auto;">
                        OCR sedang mengekstrak teks pada screenshot iklan dan mencocokkannya dengan target kriteria pengadaan. Halaman akan diperbarui otomatis.
                    </div>
                </div>
            </div>
        @elseif($candidate->ai_status === 'failed')
            <div style="background: var(--danger-bg); border: 1px solid var(--danger-border); border-radius: 6px; padding: 16px; margin-bottom: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" style="flex-shrink:0; margin-top: 1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 600; color: var(--danger);">Analisis Gambar Gagal</div>
                        <div style="font-size: 12px; color: var(--text); margin-top: 4px;">{{ $candidate->ai_error ?: 'Terjadi kesalahan teknis saat menghubungi Vision AI API.' }}</div>
                        <div style="margin-top: 10px;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="triggerReanalyze()">
                                ↺ Coba Analisis Ulang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($candidate->ai_status === 'done')
            {{-- Overall Score Card --}}
            <div class="card" style="margin-bottom: 16px; border-left: 4px solid var(--primary);">
                <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; padding: 18px 20px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--muted);">Skor Total Kesesuaian</div>
                        <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">Berdasarkan kalkulasi bobot kriteria pengadaan</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 32px; font-weight: 700; color: var(--primary); line-height: 1;">
                            {{ number_format($candidate->total_score, 1) }}
                        </span>
                        <span style="font-size: 13px; color: var(--muted); font-weight: 500;">/ 100</span>
                    </div>
                </div>
            </div>

            {{-- Breakdown per Criteria --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Rincian Evaluasi per Kriteria</span>
                    <span class="ai-badge">Evaluasi AI</span>
                </div>
                <div class="card-body" style="padding: 0;">
                    @forelse($candidate->scores->sortByDesc('score') as $score)
                        <div style="padding: 14px 18px; border-bottom: 1px solid var(--border);">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 6px;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 13px; font-weight: 600; color: var(--text);">
                                        {{ $score->criterion->name }}
                                        <span class="badge badge-gray" style="margin-left: 6px; font-size: 10px;">Bobot {{ $score->criterion->weight }}%</span>
                                    </div>
                                    <div style="font-size: 12px; color: var(--muted); margin-top: 3px;">
                                        Target: <span style="color: var(--text); font-weight: 500;">{{ $score->criterion->target }}</span>
                                    </div>
                                    <div style="font-size: 12px; color: var(--primary); margin-top: 1px;">
                                        Ditemukan: <strong>{{ $score->extracted_value ?? '— Tidak tertera' }}</strong>
                                    </div>
                                </div>
                                <div style="min-width: 110px; text-align: right;">
                                    <div style="font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 4px;">
                                        {{ number_format($score->score, 0) }} <span style="font-size: 11px; color: var(--muted); font-weight: 400;">/ 100</span>
                                    </div>
                                    @php $fc = $score->score >= 70 ? '' : ($score->score >= 40 ? 'mid' : 'low'); @endphp
                                    <div class="score-bar">
                                        <div class="score-bar-track">
                                            <div class="score-bar-fill {{ $fc }}" style="width: {{ $score->score }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($score->reasoning)
                                <div style="font-size: 11.5px; color: var(--muted); background: var(--sidebar); padding: 6px 10px; border-radius: 4px; margin-top: 6px; line-height: 1.4;">
                                    💬 <em>{{ $score->reasoning }}</em>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="empty-state" style="padding: 24px;">
                            <div class="empty-state-desc">Belum ada skor kriteria.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
@if(in_array($candidate->ai_status, ['pending', 'processing']))
<script>
const statusUrl = @json(route('procurements.candidates.status', [$procurement, $candidate]));
const pollTimer = setInterval(async () => {
    try {
        const res  = await fetch(statusUrl);
        const data = await res.json();
        if (data.ai_status === 'done' || data.ai_status === 'failed') {
            clearInterval(pollTimer);
            window.location.reload();
        }
    } catch {}
}, 3500);
</script>
@endif

<script>
async function triggerReanalyze() {
    const btn = document.getElementById('btn-reanalyze');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner" style="margin-right:4px;"></span> Mengirim...`;
    }
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const url  = @json(route('procurements.candidates.reanalyze', [$procurement, $candidate]));
    try {
        await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } });
        Swal.fire({
            icon: 'info',
            title: 'Analisis Dijadwalkan',
            text: 'AI sedang memproses ulang screenshot iklan produk ini.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => window.location.reload());
    } catch {
        window.location.reload();
    }
}

document.getElementById('btn-reanalyze')?.addEventListener('click', triggerReanalyze);
</script>
@endpush
