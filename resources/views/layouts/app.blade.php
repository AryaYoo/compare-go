<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Compare-Go') — Analisis & Evaluasi Produk AI</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
<div class="app-layout">

    {{-- Mobile Top Navbar --}}
    <header class="mobile-navbar">
        <button id="mobile-menu-toggle" class="mobile-nav-btn" aria-label="Buka Menu" title="Menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <a href="{{ route('home') }}" class="mobile-brand">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0D9488" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <path d="M16 16l3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"></path>
                <path d="M2 16l3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"></path>
                <path d="M7 21h10"></path>
                <path d="M12 3v18"></path>
                <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"></path>
            </svg>
            <span>Compare-Go</span>
        </a>

        <div class="user-avatar" style="width:28px;height:28px;font-size:11px;background:#0D9488;color:#FFFFFF;border:none;" title="{{ auth()->user()->name ?? 'User' }}">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    </header>

    {{-- Mobile Backdrop Overlay --}}
    <div id="sidebar-backdrop" class="sidebar-backdrop" onclick="closeMobileSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('home') }}" class="brand-logo" style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:8px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0D9488" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                    <path d="M16 16l3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"></path>
                    <path d="M2 16l3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1z"></path>
                    <path d="M7 21h10"></path>
                    <path d="M12 3v18"></path>
                    <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"></path>
                </svg>
                <span class="brand-text">Compare-Go</span>
            </a>
            <button id="sidebar-toggle" class="sidebar-toggle-btn desktop-only" title="Toggle Sidebar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <button class="mobile-close-btn mobile-only" onclick="closeMobileSidebar()" title="Tutup Menu">
                ✕
            </button>
        </div>

        <nav class="sidebar-nav">
            {{-- Home Menu --}}
            <a href="{{ route('home') }}"
               class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}" title="Home">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Home</span>
            </a>

            {{-- Pengadaan Barang Menu --}}
            <a href="{{ route('procurements.index') }}"
               class="nav-item {{ request()->routeIs('procurements.*') ? 'active' : '' }}" title="Pengadaan Barang">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="M9 14l2 2 4-4"></path>
                </svg>
                <span>Pengadaan Barang</span>
            </a>

            @if(auth()->check() && auth()->user()->isAdmin())
                {{-- Pengaturan Menu (Khusus Admin) --}}
                <a href="{{ route('settings.index') }}"
                   class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" title="Pengaturan">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    <span>Pengaturan</span>
                </a>
            @endif
        </nav>

        {{-- Live Real-time Clock Widget --}}
        <div class="sidebar-clock">
            <div id="sidebar-live-time">00:00:00</div>
            <div id="sidebar-live-date">-- --- ----</div>
        </div>

        {{-- Sidebar User Info --}}
        <div class="sidebar-user">
            <div class="user-info">
                <div class="user-avatar" style="background:#0D9488;color:#FFFFFF;border:none;" title="{{ auth()->user()->name ?? 'User' }}">
                    {{ strtoupper(substr(auth()->user()->name ?? (auth()->user()->username ?? 'U'), 0, 1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name ?? (auth()->user()->username ?? 'Staff IT') }}</div>
                    <div class="user-meta">
                        @if(auth()->user()->username === 'it')
                            IT Support &bull; Staff
                        @elseif(auth()->user()->username === 'admin')
                            Admin Operasional
                        @else
                            {{ auth()->user()->email }}
                        @endif
                    </div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 10px;">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="content-area">
            @yield('content')
        </div>
    </main>

    {{-- Floating Scroll to Top Button --}}
    <button id="btn-scroll-top" class="btn-scroll-top" onclick="window.scrollTo({top:0, behavior:'smooth'})" title="Ke Atas">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
    </button>

</div>

{{-- SweetAlert2 Notification --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '{{ session('success') }}',
    timer: 2500,
    showConfirmButton: false,
    confirmButtonColor: '#15803D'
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '{{ session('error') }}',
    confirmButtonColor: '#15803D'
});
</script>
@endif

@if($errors->any())
<script>
Swal.fire({
    icon: 'error',
    title: 'Validasi Gagal',
    html: '{!! implode("<br>", $errors->all()) !!}',
    confirmButtonColor: '#15803D'
});
</script>
@endif

@stack('scripts')

<script>
    // Live Sidebar Clock
    function updateSidebarClock() {
        const now = new Date();
        const timeEl = document.getElementById('sidebar-live-time');
        const dateEl = document.getElementById('sidebar-live-date');
        
        if (timeEl) {
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            timeEl.textContent = `${h}:${m}:${s}`;
        }
        
        const dateOptions = { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' };
        const dateFormatted = now.toLocaleDateString('id-ID', dateOptions);
        
        if (dateEl) {
            dateEl.textContent = dateFormatted;
        }
    }
    updateSidebarClock();
    setInterval(updateSidebarClock, 1000);

    // Desktop sidebar collapse toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
            if (document.body.classList.contains('sidebar-collapsed')) {
                localStorage.setItem('sidebar-state', 'collapsed');
            } else {
                localStorage.setItem('sidebar-state', 'expanded');
            }
        });
    }

    // Restore desktop collapsed state
    if (localStorage.getItem('sidebar-state') === 'collapsed' && window.innerWidth > 768) {
        document.body.classList.add('sidebar-collapsed');
    }

    // Mobile Sidebar Drawer
    function openMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && backdrop) {
            sidebar.classList.add('mobile-open');
            backdrop.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && backdrop) {
            sidebar.classList.remove('mobile-open');
            backdrop.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', openMobileSidebar);
    }

    // Scroll to Top Button
    const scrollTopBtn = document.getElementById('btn-scroll-top');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 240) {
            scrollTopBtn.classList.add('visible');
        } else {
            scrollTopBtn.classList.remove('visible');
        }
    });
</script>
</body>
</html>
