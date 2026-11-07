<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Kafeinarts') - {{ config('app.name', 'Kafeinarts EMS') }}</title>

    <script>
        (function () {
            var theme = localStorage.getItem('theme');
            if (theme !== 'dark' && theme !== 'light') {
                theme = 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--bs-body-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--bs-body-color);
            transition: background-color .2s ease, color .2s ease;
        }

        main {
            flex: 1 0 auto;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        /* ---------- Public navbar (Beranda) ---------- */
        .public-navbar {
            background: linear-gradient(180deg, #0f172a 0%, #121e35 100%);
            border-bottom: 1px solid rgba(255, 255, 255, .09);
            backdrop-filter: blur(8px);
        }

        .public-nav-link {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: rgba(255, 255, 255, .78);
            text-decoration: none;
            padding: .45rem .85rem;
            border-radius: 999px;
            font-size: .9rem;
            font-weight: 500;
            transition: all .15s ease;
        }

        .public-nav-link:hover,
        .public-nav-link:focus {
            color: #fff;
            background: rgba(255, 255, 255, .1);
        }

        .public-nav-link.active {
            color: #fbbf24;
            background: rgba(251, 191, 36, .14);
            font-weight: 600;
        }

        .public-search {
            min-width: 200px;
            max-width: 260px;
        }

        .public-search .form-control {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .14);
            color: #fff;
            border-radius: 999px;
            padding-left: .9rem;
        }

        .public-search .form-control::placeholder {
            color: rgba(255, 255, 255, .45);
        }

        .public-search .form-control:focus {
            background: rgba(255, 255, 255, .13);
            border-color: rgba(251, 191, 36, .65);
            color: #fff;
            box-shadow: 0 0 0 .2rem rgba(251, 191, 36, .18);
        }

        .offcanvas .public-chip {
            text-decoration: none;
        }

        .offcanvas .public-chip:hover {
            border-color: #fbbf24;
            color: #fbbf24;
        }

        .card {
            border-radius: 0.75rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color .2s ease;
        }

        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem -.75rem rgba(15, 23, 42, .28) !important;
        }

        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .badge {
            font-weight: 500;
            border-radius: 0.375rem;
        }

        .table th {
            font-weight: 600;
            color: var(--bs-secondary-color);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        footer {
            margin-top: auto;
            background-color: var(--bs-body-bg);
            border-top: 1px solid var(--bs-border-color);
        }

        /* ---------- Theme toggle ---------- */
        .theme-icon-sun {
            display: none;
        }

        [data-bs-theme="dark"] .theme-icon-sun {
            display: inline-block;
        }

        [data-bs-theme="dark"] .theme-icon-moon {
            display: none;
        }

        /* ---------- Admin shell ---------- */
        .admin-shell {
            --sidebar-width: 264px;
            display: flex;
            flex: 1 0 auto;
            min-height: 100vh;
            width: 100%;
        }

        .admin-main {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }

        .admin-content {
            flex: 1 0 auto;
        }

        .admin-sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bs-body-bg);
            border-right: 1px solid var(--bs-border-color);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1040;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--bs-border-color);
        }

        .sidebar-header .brand {
            color: var(--bs-body-color);
            font-size: 1.05rem;
        }

        .sidebar-body {
            flex: 1 0 auto;
            padding: 1rem .75rem;
        }

        .sidebar-section-title {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--bs-secondary-color);
            font-weight: 600;
            padding: .75rem .75rem .35rem;
        }

        .admin-sidebar .nav-link {
            color: var(--bs-body-color);
            border-radius: .5rem;
            padding: .55rem .75rem;
            font-size: .925rem;
            display: flex;
            align-items: center;
        }

        .admin-sidebar .nav-link:hover {
            background-color: var(--bs-secondary-bg);
        }

        .admin-sidebar .nav-link.active {
            background-color: rgba(var(--bs-primary-rgb), .15);
            color: var(--bs-primary);
            font-weight: 600;
        }

        .admin-sidebar .nav-link.disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--bs-border-color);
        }

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1.5rem;
            background-color: var(--bs-body-bg);
            border-bottom: 1px solid var(--bs-border-color);
        }

        .admin-topbar-title {
            font-size: .95rem;
        }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, .45);
            z-index: 1035;
        }

        /* ---------- Sidebar hide/show (slide ke kiri) ---------- */

        .admin-sidebar {
            transition: width .3s ease;
        }

        /* Slide ke kiri — untuk layar laptop/desktop */
        @media (min-width: 768px) {
            .admin-shell.sidebar-hidden .admin-sidebar {
                flex-shrink: 1;
                min-width: 0;
                width: 0;
                overflow: hidden;
                border-right: 0;
            }
        }

        /* Tombol hide/show di header sidebar */
        .sidebar-hide-toggle {
            width: 32px;
            height: 32px;
            border: 0;
            background: var(--bs-tertiary-bg);
            color: var(--bs-secondary-color);
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            cursor: pointer;
            transition: all .2s ease;
        }

        .sidebar-hide-toggle:hover {
            background: var(--bs-secondary-bg);
            color: var(--bs-body-color);
        }

        /* FAB reveal — hanya muncul saat sidebar hidden */
        .sidebar-reveal-fab {
            position: fixed;
            left: .75rem;
            bottom: .75rem;
            z-index: 1060;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 0;
            background: var(--bs-primary);
            color: #fff;
            box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .3);
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            cursor: pointer;
            transition: transform .2s ease;
        }

        .sidebar-reveal-fab:hover {
            transform: scale(1.08);
        }

        .admin-shell.sidebar-hidden .sidebar-reveal-fab {
            display: inline-flex;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                transition: transform .2s ease;
                box-shadow: 0 0 2rem rgba(0, 0, 0, .25);
            }

            .admin-shell.sidebar-open .admin-sidebar {
                transform: translateX(0);
            }

            .admin-topbar {
                padding: .75rem 1rem;
            }
        }

        /* ---------- Event wizard (vertical sidebar) ---------- */
        .wizard-vsteps {
            position: relative;
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        /* Garis penghubung di belakang lingkaran nomor — dihilangkan agar lebih rapi */

        .wizard-vstep {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .55rem .65rem;
            border: 1px solid transparent;
            border-radius: .8rem;
            background: transparent;
            color: var(--bs-secondary-color);
            font-size: .875rem;
            transition: transform .2s ease, background-color .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .wizard-vstep-number {
            width: 2.1rem;
            height: 2.1rem;
            flex: 0 0 2.1rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-body-tertiary-bg);
            border: 1px solid var(--bs-border-color);
            color: var(--bs-secondary-color);
            font-weight: 700;
            font-size: .85rem;
            transition: all .25s ease;
        }

        .wizard-vstep-body {
            min-width: 0;
            flex: 1;
        }

        .wizard-vstep-label {
            display: block;
            font-weight: 600;
            color: var(--bs-body-color);
            white-space: nowrap;
            transition: color .2s ease;
        }

        .wizard-vstep-hint {
            display: block;
            font-size: .68rem;
            color: var(--bs-secondary-color);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wizard-vstep-arrow {
            flex: 0 0 auto;
            opacity: .3;
            font-size: .9rem;
            transition: transform .25s ease, opacity .25s ease, color .25s ease;
        }

        .wizard-vstep[role="button"] {
            cursor: pointer;
        }

        /* Hover */
        .wizard-vstep:is([role="button"], :hover) {
            background-color: rgba(var(--bs-primary-rgb), .07);
            border-color: rgba(var(--bs-primary-rgb), .35);
            transform: translateX(6px);
            box-shadow: 0 .3rem .6rem rgba(0, 0, 0, .07);
        }

        .wizard-vstep:is([role="button"], :hover) .wizard-vstep-arrow {
            opacity: 1;
            transform: translateX(4px);
            color: var(--bs-primary);
        }

        .wizard-vstep:focus-visible {
            outline: 2px solid var(--bs-primary);
            outline-offset: 2px;
        }

        /* Active */
        .wizard-vstep.active {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), .18), rgba(var(--bs-primary-rgb), .05));
            border-color: rgba(var(--bs-primary-rgb), .5);
            box-shadow: 0 .35rem .75rem rgba(var(--bs-primary-rgb), .18);
            transform: translateX(6px);
        }

        .wizard-vstep.active .wizard-vstep-number {
            background: var(--bs-primary);
            border-color: transparent;
            color: #fff;
            transform: scale(1.12);
            box-shadow: 0 .25rem .6rem rgba(var(--bs-primary-rgb), .45);
        }

        .wizard-vstep.active .wizard-vstep-label {
            color: var(--bs-primary);
        }

        .wizard-vstep.active .wizard-vstep-arrow {
            opacity: 1;
            color: var(--bs-primary);
        }

        /* Completed */
        .wizard-vstep.completed .wizard-vstep-number {
            background-color: var(--bs-success);
            border-color: transparent;
            color: #fff;
        }

        .wizard-vstep.completed .wizard-vstep-label {
            color: var(--bs-success-text-emphasis);
        }

        .wizard-vstep.completed .wizard-vstep-arrow {
            color: var(--bs-success-text-emphasis);
        }

        @media (max-width: 767.98px) {
            .event-wizard-layout .col-md-3 {
                width: 100%;
            }

            .wizard-vsteps {
                flex-direction: row;
                overflow-x: auto;
                gap: .45rem;
                padding-bottom: .35rem;
            }

            .wizard-vstep {
                flex: 0 0 auto;
                padding: .45rem .55rem;
            }

            .wizard-vstep-hint,
            .wizard-vstep-arrow {
                display: none;
            }

            .wizard-vstep-number {
                width: 1.75rem;
                height: 1.75rem;
                flex-basis: 1.75rem;
                font-size: .75rem;
            }
        }

        .wizard-pane + .wizard-pane {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed var(--bs-border-color);
        }

        .wizard-pane-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: .6rem;
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }

        .media-preview {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: .6rem;
            border: 1px dashed var(--bs-border-color);
            background-color: var(--bs-secondary-bg);
        }

        .avatar-preview {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--bs-border-color);
            background-color: var(--bs-secondary-bg);
        }

        .event-type-card {
            cursor: pointer;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }

        .event-type-card:hover {
            border-color: var(--bs-primary);
        }

        .event-type-card:has(input:checked) {
            border-color: var(--bs-primary);
            background-color: rgba(var(--bs-primary-rgb), .08);
            box-shadow: 0 0 0 .15rem rgba(var(--bs-primary-rgb), .15);
        }

        /* Mode cetak: sembunyikan chrome halaman, hanya konten yang dicetak. */
        @media print {
            .navbar,
            footer,
            .no-print,
            .btn,
            .sidebar-backdrop {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            main,
            .admin-content {
                padding: 0 !important;
                margin: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    @if (request()->is('admin*'))
        <div class="admin-shell" id="adminShell">
            @include('components.admin-sidebar')

            <div class="admin-main">
                @include('components.admin-nav')

                <main class="admin-content pb-4">
                    <div class="container-fluid pt-3">
                        @include('components.session-alerts')
                    </div>

                    @yield('content')
                </main>

                <footer class="py-3">
                    <div class="container-fluid text-center text-muted small">
                        &copy; {{ date('Y') }} <strong>Kafeinarts</strong> Event Management System. All rights reserved.
                    </div>
                </footer>
            </div>
        </div>
    @else
        @php
            $navCategories = \Illuminate\Support\Facades\Cache::remember(
                'public.nav.categories.v2',
                300,
                fn () => \App\Models\EventCategory::where('is_active', true)->orderBy('name')->limit(8)->get(['name', 'slug'])->map(fn ($category) => ['name' => $category->name, 'slug' => $category->slug])->all()
            );
        @endphp

        <nav class="navbar public-navbar navbar-dark sticky-top shadow-sm" aria-label="Navigasi utama">
            <div class="container d-flex align-items-center justify-content-between gap-2">
                <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}">
                    <i class="bi bi-ticket-perforated-fill text-warning fs-4"></i>
                    <span>Kafeinarts<span class="text-warning">EMS</span></span>
                </a>

                {{-- Desktop navigation --}}
                <div class="d-none d-lg-flex align-items-center gap-2">
                    <ul class="navbar-nav flex-row gap-1 mb-0 align-items-center">
                        <li class="nav-item">
                            <a class="public-nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>
                                <i class="bi bi-house-door"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="public-nav-link {{ request()->routeIs('public.events.*') ? 'active' : '' }}"
                                href="{{ route('public.events.index') }}">
                                <i class="bi bi-calendar-event"></i>Event
                            </a>
                        </li>
                       {{--  @if (count($navCategories))
                            <li class="nav-item dropdown">
                                <a class="public-nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-grid"></i>Kategori
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark shadow border-0 mt-2">
                                    <li>
                                        <h6 class="dropdown-header"><i class="bi bi-tag me-1"></i>Jelajahi per kategori</h6>
                                    </li>
                                    @foreach ($navCategories as $category)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('public.events.index', ['category' => $category['slug']]) }}">
                                                <i class="bi bi-chevron-right small me-2 text-warning"></i>{{ $category['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif --}}
                    </ul>

                    <form action="{{ route('public.events.index') }}" method="GET" class="public-search d-flex" role="search">
                        <input type="search" name="search" class="form-control form-control-sm"
                            placeholder="Cari event…" value="{{ request('search') }}" aria-label="Cari event">
                        <button type="submit" class="btn btn-warning btn-sm ms-2 fw-semibold" aria-label="Cari">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" aria-label="Ganti bahasa">
                            <i class="bi bi-translate me-1"></i>{{ strtoupper(app()->getLocale() ?? 'id') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('locale.switch', 'id') }}">Bahasa Indonesia (ID)</a></li>
                            <li><a class="dropdown-item" href="{{ route('locale.switch', 'en') }}">English (EN)</a></li>
                        </ul>
                    </div>

                    <x-theme-toggle variant="btn-outline-light" />

                    <a href="{{ route('public.events.index') }}" class="btn btn-warning btn-sm fw-bold">
                        <i class="bi bi-ticket-perforated me-1"></i>Beli Tiket
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm"
                        title="Masuk ke panel admin" aria-label="Admin Dashboard">
                        <i class="bi bi-speedometer2"></i>
                    </a>
                </div>

                {{-- Mobile offcanvas toggle --}}
                <button class="navbar-toggler border-0 d-lg-none ps-2" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas"
                    aria-controls="navbarOffcanvas" aria-label="Toggle navigation">
                    <i class="bi bi-list text-white fs-2"></i>
                </button>
            </div>
        </nav>

        {{-- Mobile offcanvas slide from left --}}
        <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="navbarOffcanvas"
            aria-labelledby="navbarOffcanvasLabel">
            <div class="offcanvas-header border-bottom border-secondary py-3">
                <h5 class="offcanvas-title text-warning" id="navbarOffcanvasLabel">
                    <i class="bi bi-ticket-perforated-fill me-2"></i>Kafeinarts EMS
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body">
                <form action="{{ route('public.events.index') }}" method="GET" class="d-flex mb-4" role="search">
                    <input type="search" name="search" class="form-control" placeholder="Cari event…"
                        value="{{ request('search') }}" aria-label="Cari event">
                    <button type="submit" class="btn btn-warning ms-2" aria-label="Cari">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <div class="sidebar-section-title">Menu</div>
                <ul class="navbar-nav gap-1 mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'text-warning fw-bold' : 'text-white' }}"
                            href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>
                            <i class="bi bi-house-door me-2"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.events.*') ? 'text-warning fw-bold' : 'text-white' }}"
                            href="{{ route('public.events.index') }}">
                            <i class="bi bi-calendar-event me-2"></i>Event
                        </a>
                    </li>
                </ul>

                @if (count($navCategories))
                    <div class="sidebar-section-title">Kategori Event</div>
                    <div class="d-flex flex-wrap gap-2 mb-4 px-2">
                        @foreach ($navCategories as $category)
                            <a href="{{ route('public.events.index', ['category' => $category['slug']]) }}"
                                class="public-chip btn btn-sm btn-outline-light rounded-pill">
                                {{ $category['name'] }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="sidebar-section-title">Aksi</div>
                <div class="d-grid gap-2 mb-4 px-2">
                    <a class="btn btn-warning" href="{{ route('public.events.index') }}">
                        <i class="bi bi-ticket-perforated me-1"></i>Jelajahi &amp; Beli Tiket
                    </a>
                    <a class="btn btn-outline-light" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
                    </a>
                </div>

                <div class="d-flex align-items-center justify-content-between gap-2 px-2 border-top border-secondary pt-3">
                    <x-theme-toggle variant="btn-outline-light" />
                    <div class="dropdown">
                        <a class="btn btn-outline-light btn-sm dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate me-1"></i>{{ strtoupper(app()->getLocale() ?? 'id') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="{{ route('locale.switch', 'id') }}">Bahasa Indonesia (ID)</a></li>
                            <li><a class="dropdown-item" href="{{ route('locale.switch', 'en') }}">English (EN)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <main class="py-3">
            <div class="container">
                @include('components.session-alerts')
            </div>

            @yield('content')
        </main>

        <footer class="py-4">
            <div class="container text-center text-muted small">
                &copy; {{ date('Y') }} <strong>Kafeinarts</strong> Event Management System. All rights reserved.
            </div>
        </footer>
    @endif

    <!-- Bootstrap 5.3.3 JS Bundle CDN (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
        (function () {
            function currentTheme() {
                return document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
            }

            function applyTheme(theme) {
                document.documentElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
            }

            document.addEventListener('click', function (event) {
                var toggle = event.target.closest('[data-theme-toggle]');
                if (toggle) {
                    applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
                    return;
                }

                if (event.target.closest('[data-sidebar-open]')) {
                    var shell = document.getElementById('adminShell');
                    if (shell) {
                        shell.classList.add('sidebar-open');
                        var backdrop = document.querySelector('.sidebar-backdrop');
                        if (backdrop) backdrop.hidden = false;
                    }
                    return;
                }

                if (event.target.closest('[data-sidebar-close]')) {
                    var openShell = document.getElementById('adminShell');
                    if (openShell) {
                        openShell.classList.remove('sidebar-open');
                        var bd = document.querySelector('.sidebar-backdrop');
                        if (bd) bd.hidden = true;
                    }
                }
            });
        })();
    </script>

    @stack('scripts')
</body>

</html>
