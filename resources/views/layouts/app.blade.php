<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Master Jaya')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --mj-navy: #1F3349;
            --mj-gold: #D99A1B;
            --mj-red: #DC3545;
        }
        [data-bs-theme="light"] {
            --mj-bg: #F5F6FA;
            --mj-card: #FFFFFF;
            --mj-text: #1F3349;
            --mj-text-muted: #6C757D;
            --mj-border: #DEE2E6;
            --mj-sidebar-bg: #1F3349;
            --mj-sidebar-text: #FFFFFF;
            --mj-sidebar-active: #D99A1B;
        }
        [data-bs-theme="dark"] {
            --mj-bg: #0F1A2E;
            --mj-card: #1A2A44;
            --mj-text: #E8E8E8;
            --mj-text-muted: #A0AEC0;
            --mj-border: #2D3E56;
            --mj-sidebar-bg: #0A1220;
            --mj-sidebar-text: #E8E8E8;
            --mj-sidebar-active: #D99A1B;
        }
        body {
            background-color: var(--mj-bg);
            color: var(--mj-text);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .navbar-mj {
            background-color: var(--mj-navy) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-mj .navbar-brand { color: var(--mj-gold) !important; font-weight: 700; font-size: 1.3rem; }
        .sidebar {
            background-color: var(--mj-sidebar-bg);
            min-height: calc(100vh - 56px);
            padding-top: 1rem;
        }
        .sidebar .nav-link {
            color: var(--mj-sidebar-text);
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(217, 154, 27, 0.15);
            color: var(--mj-sidebar-active);
        }
        .sidebar .nav-link i { font-size: 1.2rem; }
        .card {
            background-color: var(--mj-card);
            border: 1px solid var(--mj-border);
            border-radius: 0.5rem;
        }
        .card-summary {
            border-left: 4px solid var(--mj-gold);
        }
        .card-summary .card-title { font-size: 0.85rem; color: var(--mj-text-muted); }
        .card-summary .card-value { font-size: 1.5rem; font-weight: 700; }
        .btn-mj { background-color: var(--mj-navy); color: #fff; border: none; }
        .btn-mj:hover { background-color: #162840; color: #fff; }
        .btn-gold { background-color: var(--mj-gold); color: #fff; border: none; }
        .btn-gold:hover { background-color: #C48A17; color: #fff; }
        .table { color: var(--mj-text); }
        .table th { font-weight: 600; font-size: 0.9rem; }
        .badge-menipis { background-color: var(--mj-gold); color: #fff; }
        .badge-habis { background-color: var(--mj-red); color: #fff; }
        .badge-normal { background-color: #198754; color: #fff; }
        .main-content { padding: 1.5rem; }
        @media (max-width: 768px) {
            .sidebar { min-height: auto; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-mj navbar-expand-lg sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ auth()->user()->isAdmin() ? route('beranda') : route('penjualan.index') }}">
                <i class="bi bi-shop"></i> Master Jaya
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navCollapse">
                {{-- Mobile nav for kasir/admin --}}
                <ul class="navbar-nav d-lg-none">
                    @yield('mobile-nav')
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="nav-link text-white border-0 bg-transparent" onclick="toggleTheme()" title="Mode Gelap/Terang">
                            <i class="bi bi-moon-fill" id="themeIcon"></i>
                            <span class="d-lg-none ms-2">Mode Gelap/Terang</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link text-white-50 d-none d-lg-block">{{ auth()->user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-link text-white border-0 bg-transparent" type="submit">
                                <i class="bi bi-box-arrow-right"></i>
                                <span class="ms-1">Keluar</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar (desktop) --}}
            <div class="col-lg-2 d-none d-lg-block sidebar">
                @yield('sidebar')
            </div>

            <div class="col-lg-10 main-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        @foreach($errors->all() as $error) {{ $error }} @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(next);
        }
        function updateThemeIcon(theme) {
            const icon = document.getElementById('themeIcon');
            icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }
        (function() {
            const saved = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', saved);
            updateThemeIcon(saved);
        })();
    </script>
    @yield('scripts')
</body>
</html>
