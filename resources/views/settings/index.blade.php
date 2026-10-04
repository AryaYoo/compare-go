@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="page-header" style="align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 20px; font-weight: 700;">Pengaturan Sistem</h1>
        <div class="page-header-sub" style="font-size: 13px; color: var(--muted); margin-top: 3px;">
            Konfigurasi preferensi sistem dan visibilitas tampilan login
        </div>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 20px; max-width: 900px;">

    {{-- CARD 1: TAMPILAN & AKSESIBILITAS LOGIN --}}
    <div class="settings-card">
        <div class="settings-card-header" onclick="toggleSection('login-settings-body', 'login-chevron')">
            <div>
                <div class="settings-card-title">Tampilan & Aksesibilitas Login</div>
                <div class="settings-card-desc">Atur elemen yang tampil untuk pengguna umum saat membuka form autentikasi</div>
            </div>
            <button type="button" class="btn-toggle-expand" id="login-chevron" aria-label="Toggle section">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
        </div>

        <div class="settings-card-body" id="login-settings-body">
            {{-- Setting Item: Toggle Demo Info --}}
            <div class="setting-row">
                <div style="flex: 1; padding-right: 20px;">
                    <div style="font-size: 13.5px; font-weight: 600; color: var(--text); margin-bottom: 4px;">
                        Tampilkan Info Akun Demo
                    </div>
                    <div style="font-size: 12.5px; color: var(--muted); line-height: 1.45;">
                        Menampilkan informasi bantuan kredensial default demo (<em>it</em> &amp; <em>admin@hsitoperasional.com</em>) di bawah tombol masuk pada halaman login.
                    </div>
                </div>
                <div>
                    <label class="switch">
                        <input type="checkbox" id="toggle-demo-switch" {{ $showDemoAccounts ? 'checked' : '' }} onchange="handleToggleDemo(this)">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            {{-- Live Preview Box --}}
            <div class="preview-box">
                <div class="preview-box-header">
                    <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.6px; color: #64748B; text-transform: uppercase;">
                        PREVIEW ELEMEN HALAMAN LOGIN:
                    </span>
                    <span id="preview-status-pill" class="status-pill {{ $showDemoAccounts ? 'pill-visible' : 'pill-hidden' }}">
                        {{ $showDemoAccounts ? '● Ditampilkan (Aktif)' : '○ Tersembunyi (Disembunyikan)' }}
                    </span>
                </div>

                <div id="demo-preview-content" class="preview-inner-box {{ $showDemoAccounts ? '' : 'is-faded' }}">
                    <div style="font-size: 11.5px; color: #64748B; line-height: 1.6;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span>Demo User: <strong>it</strong> / it</span>
                            <span style="font-size: 10.5px; background: #F1F5F9; padding: 2px 6px; border-radius: 4px;">Staff IT</span>
                        </div>
                        <div style="border-top: 1px dashed #E2E8F0; padding-top: 4px; display: flex; justify-content: space-between; align-items: center;">
                            <span>Demo Admin: <strong>admin@hsitoperasional.com</strong> / admin</span>
                            <span style="font-size: 10.5px; background: #F1F5F9; padding: 2px 6px; border-radius: 4px;">Admin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 2: PEMANTAUAN KUOTA GEMINI AI --}}
    <div class="settings-card">
        <div class="settings-card-header" onclick="toggleSection('ai-settings-body', 'ai-chevron')">
            <div>
                <div class="settings-card-title">Pemantauan Kuota Gemini AI (Analisis Gambar)</div>
                <div class="settings-card-desc">Estimasi penggunaan request &amp; token harian untuk analisis perbandingan produk (Google Free Tier)</div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span class="ai-status-pill">
                    ● Siap Digunakan
                </span>
                <button type="button" class="btn-toggle-expand" id="ai-chevron" aria-label="Toggle section">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <div class="settings-card-body" id="ai-settings-body">
            {{-- 4 Stat Grid Cards --}}
            <div class="stat-grid">
                <div class="stat-box">
                    <div class="stat-box-label">Request Hari Ini</div>
                    <div class="stat-box-value">
                        <span style="color: var(--text);">{{ $requestsToday }}</span>
                        <span style="font-size: 13px; font-weight: 500; color: var(--muted);">/ {{ $maxDailyRequests }}</span>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Sisa Request</div>
                    <div class="stat-box-value" style="color: #0D9488;">
                        {{ $remainingRequests }} <span style="font-size: 13px; font-weight: 500; color: #0F766E;">tersisa</span>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Token Terpakai</div>
                    <div class="stat-box-value" style="color: var(--text);">
                        {{ number_format($tokensToday) }}
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Model Aktif</div>
                    <div class="stat-box-value model-name" style="font-size: 14.5px; font-family: monospace; color: var(--text);">
                        {{ $activeModel }}
                    </div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div style="margin-top: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-size: 12px; font-weight: 500; color: var(--muted);">Beban Kuota Harian</span>
                    <span style="font-size: 12px; font-weight: 600; color: var(--text);">{{ $quotaPercent }}%</span>
                </div>
                <div class="quota-track">
                    <div class="quota-fill" style="width: {{ $quotaPercent }}%; background-color: {{ $quotaPercent > 80 ? '#DC2626' : ($quotaPercent > 60 ? '#D97706' : '#0D9488') }};"></div>
                </div>
            </div>

            {{-- Footer Info & Action Buttons --}}
            <div class="settings-card-footer">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--muted);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="#0D9488" style="flex-shrink: 0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <span>Kuota harian akun gratis di-reset otomatis setiap pukul 07:00 WIB.</span>
                </div>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <form method="POST" action="{{ route('settings.resetAiUsage') }}" onsubmit="return confirm('Reset penghitungan kuota request AI hari ini?')">
                        @csrf
                        <button type="submit" class="link-btn" title="Reset angka hitungan">
                            Reset Hitungan
                        </button>
                    </form>
                    <a href="https://aistudio.google.com/" target="_blank" rel="noopener noreferrer" class="link-btn-external" title="Buka Google AI Studio">
                        <span>Cek Resmi di AI Studio</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.settings-card {
    background: #FFFFFF;
    border: 1px solid var(--border);
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.settings-card-header {
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    cursor: pointer;
    user-select: none;
    transition: background 0.15s ease;
}

.settings-card-header:hover {
    background-color: #FAFAFC;
}

.settings-card-title {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 2px;
}

.settings-card-desc {
    font-size: 12.5px;
    color: var(--muted);
}

.settings-card-body {
    padding: 0 22px 22px 22px;
    border-top: 1px solid #F1F5F9;
    transition: all 0.2s ease-in-out;
}

.btn-toggle-expand {
    background: transparent;
    border: 1px solid var(--border);
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-toggle-expand:hover {
    background: var(--sidebar);
    color: var(--text);
}

.btn-toggle-expand.collapsed svg {
    transform: rotate(-90deg);
}

.setting-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 0;
    border-bottom: 1px solid #F8FAFC;
}

/* Switch styling */
.switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #CBD5E1;
    transition: .25s ease;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .25s ease;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

input:checked + .slider {
    background-color: #0D9488;
}

input:checked + .slider:before {
    transform: translateX(20px);
}

/* Preview Box */
.preview-box {
    margin-top: 14px;
    border: 1px dashed #CBD5E1;
    border-radius: 8px;
    padding: 16px;
    background: #FAFAFC;
}

.preview-box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.status-pill {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 4px;
}

.pill-visible {
    color: #0D9488;
    background: #F0FDFA;
}

.pill-hidden {
    color: #DC2626;
    background: #FEF2F2;
}

.preview-inner-box {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    padding: 12px 14px;
    transition: opacity 0.2s ease, filter 0.2s ease;
}

.preview-inner-box.is-faded {
    opacity: 0.35;
    filter: grayscale(1);
}

/* Stat Grid */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-top: 16px;
}

@media (max-width: 768px) {
    .stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.stat-box {
    background: #FAFAFC;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    padding: 12px 14px;
}

.stat-box-label {
    font-size: 11px;
    font-weight: 500;
    color: var(--muted);
    margin-bottom: 4px;
}

.stat-box-value {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -0.2px;
}

.ai-status-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    color: #15803D;
    font-size: 11px;
    font-weight: 600;
    border-radius: 20px;
    white-space: nowrap;
}

.quota-track {
    width: 100%;
    height: 6px;
    background: #E2E8F0;
    border-radius: 4px;
    overflow: hidden;
}

.quota-fill {
    height: 100%;
    transition: width 0.3s ease;
}

.settings-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 20px;
    padding-top: 14px;
    border-top: 1px solid #F1F5F9;
    flex-wrap: wrap;
    gap: 12px;
}

.link-btn {
    background: none;
    border: none;
    color: #64748B;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: underline;
    padding: 0;
    transition: color 0.15s;
}

.link-btn:hover {
    color: #DC2626;
}

.link-btn-external {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #0D9488;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.15s;
}

.link-btn-external:hover {
    color: #0F766E;
    text-decoration: underline;
}
</style>
@endpush

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function toggleSection(bodyId, chevronId) {
    const body = document.getElementById(bodyId);
    const chevron = document.getElementById(chevronId);
    if (!body) return;

    if (body.style.display === 'none') {
        body.style.display = 'block';
        if (chevron) chevron.classList.remove('collapsed');
    } else {
        body.style.display = 'none';
        if (chevron) chevron.classList.add('collapsed');
    }
}

async function handleToggleDemo(checkbox) {
    const isChecked = checkbox.checked;
    const previewStatus = document.getElementById('preview-status-pill');
    const previewContent = document.getElementById('demo-preview-content');

    // Update preview instantly
    if (isChecked) {
        previewStatus.textContent = '● Ditampilkan (Aktif)';
        previewStatus.className = 'status-pill pill-visible';
        previewContent.classList.remove('is-faded');
    } else {
        previewStatus.textContent = '○ Tersembunyi (Disembunyikan)';
        previewStatus.className = 'status-pill pill-hidden';
        previewContent.classList.add('is-faded');
    }

    try {
        const response = await fetch('{{ route('settings.toggleDemo') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ show_demo_accounts: isChecked })
        });
        const data = await response.json();
        
        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message || 'Pengaturan berhasil disimpan',
                showConfirmButton: false,
                timer: 2000
            });
        }
    } catch (err) {
        checkbox.checked = !isChecked; // Revert
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: 'Terjadi kesalahan saat menyimpan pengaturan.'
            });
        }
    }
}
</script>
@endpush
