@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="home-hero-container">
    <div class="home-hero-card">
        {{-- Blue Lightning Icon --}}
        <div class="home-hero-icon">
            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
            </svg>
        </div>

        {{-- Greeting --}}
        <h1 class="home-hero-title">
            Selamat datang, {{ auth()->user()->name ?? 'Staff IT' }} 👋
        </h1>

        {{-- Subtitle --}}
        <p class="home-hero-subtitle">
            Ringkasan pengadaan & analisis rekomendasi produk AI
        </p>

        {{-- Action Button --}}
        <div class="home-hero-actions">
            <a href="{{ route('procurements.create') }}" class="btn-home-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Catat Pengadaan</span>
            </a>
            
            <a href="{{ route('procurements.index') }}" class="btn-home-secondary">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="M9 14l2 2 4-4"></path>
                </svg>
                <span>Daftar Pengadaan ({{ $totalProcurements ?? 0 }})</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.home-hero-container {
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.home-hero-card {
    text-align: center;
    max-width: 580px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.home-hero-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #0D9488;
    margin-bottom: 22px;
    animation: pulseSlow 3s ease-in-out infinite;
}

@keyframes pulseSlow {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.home-hero-title {
    font-size: 28px;
    font-weight: 700;
    color: #0F172A;
    letter-spacing: -0.5px;
    margin-bottom: 10px;
    line-height: 1.25;
}

.home-hero-subtitle {
    font-size: 15px;
    color: #64748B;
    margin-bottom: 28px;
    line-height: 1.5;
}

.home-hero-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
}

.btn-home-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background-color: #0D9488;
    color: #FFFFFF !important;
    font-size: 14.5px;
    font-weight: 600;
    padding: 11px 24px;
    border-radius: 8px;
    text-decoration: none;
    box-shadow: 0 4px 14px 0 rgba(13, 148, 136, 0.35);
    transition: all 0.2s ease;
}

.btn-home-primary:hover {
    background-color: #0F766E;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px 0 rgba(13, 148, 136, 0.45);
}

.btn-home-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background-color: #FFFFFF;
    color: #334155 !important;
    font-size: 14px;
    font-weight: 500;
    padding: 10px 18px;
    border-radius: 8px;
    border: 1px solid #E2E8F0;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-home-secondary:hover {
    background-color: #F8FAFC;
    border-color: #CBD5E1;
    color: #0F172A !important;
}
</style>
@endpush
