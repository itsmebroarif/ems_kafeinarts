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
            width: 264px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bs-body-bg);
            border-right: 1px solid var(--bs-border-color);
            overflow-y: auto;
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

        /* ---------- Event wizard ---------- */
        .wizard-steps {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            list-style: none;
            padding: 0;
            margin: 0 0 1.75rem;
        }

        .wizard-step-item {
            flex: 1 1 160px;
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .6rem .75rem;
            border: 1px solid var(--bs-border-color);
            border-radius: .6rem;
            background-color: var(--bs-body-bg);
            color: var(--bs-secondary-color);
            font-size: .85rem;
        }

        .wizard-step-item .step-number {
            width: 1.6rem;
            height: 1.6rem;
            flex-shrink: 0;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-secondary-bg);
            color: var(--bs-secondary-color);
            font-weight: 600;
            font-size: .8rem;
        }

        .wizard-step-item.active {
            border-color: var(--bs-primary);
            color: var(--bs-primary);
            background-color: rgba(var(--bs-primary-rgb), .08);
            font-weight: 600;
        }

        .wizard-step-item.active .step-number {
            background-color: var(--bs-primary);
            color: #fff;
        }

        .wizard-step-item.completed {
            border-color: rgba(var(--bs-success-rgb), .45);
            color: var(--bs-success-text-emphasis);
        }

        .wizard-step-item.completed .step-number {
            background-color: var(--bs-success);
            color: #fff;
        }

        .wizard-pane + .wizard-pane {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed var(--bs-border-color);
        }

        .wizard-step-item[role="button"] {
            cursor: pointer;
        }

        .wizard-step-item[role="button"]:hover {
            border-color: var(--bs-primary);
        }

        .wizard-pane-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
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
        <nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}">
                    <i class="bi bi-ticket-perforated-fill text-warning fs-4"></i>
                    <span>Kafeinarts EMS</span>
                </a>

                {{-- Desktop navigation --}}
                <div class="d-none d-lg-flex align-items-center gap-3">
                    <ul class="navbar-nav flex-row gap-3 mb-0 align-items-center">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active text-warning' : 'text-white' }}"
                                href="{{ route('home') }}">
                                <i class="bi bi-house-door me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.events.*') ? 'active text-warning' : 'text-white' }}"
                                href="{{ route('public.events.index') }}">
                                <i class="bi bi-calendar-event me-1"></i>Event
                            </a>
                        </li>
                    </ul>
                    <x-theme-toggle variant="btn-outline-light" />
                    <a class="btn btn-sm btn-outline-warning" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
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
                <ul class="navbar-nav gap-1 mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'text-warning fw-bold' : 'text-white' }}"
                            href="{{ route('home') }}">
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

                <div class="d-grid gap-2 mb-4">
                    <a class="btn btn-outline-light" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Admin Dashboard
                    </a>
                </div>

                <div class="d-flex align-items-center justify-content-between gap-2 px-2">
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
