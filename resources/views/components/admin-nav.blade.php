<nav class="admin-topbar">
    <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none sidebar-toggle"
        data-sidebar-open aria-label="Buka menu">
        <i class="bi bi-list fs-5"></i>
    </button>

    <span class="admin-topbar-title fw-semibold text-truncate">
        @hasSection('title')
            @yield('title')
        @else
            Admin
        @endif
    </span>

    <div class="ms-auto d-flex align-items-center gap-2">
        <x-theme-toggle variant="btn-outline-secondary" class="d-none d-sm-inline-flex" />

        <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right me-1 d-none d-md-inline"></i>Lihat Web
        </a>

        <span class="badge bg-secondary text-truncate" style="max-width: 160px;">
            <i class="bi bi-person-circle me-1"></i>{{ Auth::check() ? Auth::user()->name : 'Admin' }}
        </span>
    </div>
</nav>
