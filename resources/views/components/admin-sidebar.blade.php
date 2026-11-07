@php
    $currentEvent = request()->route('event');
    $currentEventId = is_object($currentEvent) ? $currentEvent->id : $currentEvent;

    $eventMenus = [
        ['route' => 'admin.events.tiers.index', 'icon' => 'bi-ticket-detailed', 'label' => 'Tier Tiket'],
        ['route' => 'admin.events.addons.index', 'icon' => 'bi-bag-plus', 'label' => 'Add-on'],
        ['route' => 'admin.events.vouchers.index', 'icon' => 'bi-tag', 'label' => 'Voucher'],
        ['route' => 'admin.events.registrants.index', 'icon' => 'bi-people', 'label' => 'Pendaftar'],
        ['route' => 'admin.events.panitia.index', 'icon' => 'bi-person-badge', 'label' => 'Panitia'],
    ];
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="brand d-flex align-items-center gap-2 text-decoration-none">
            <i class="bi bi-shield-check text-warning fs-5"></i>
            <span class="fw-bold hide-in-compact">Kafeinarts</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <div class="sidebar-mode-toggle" role="group" aria-label="Mode sidebar">
                <button type="button" class="mode-btn" data-mode-toggle="full" title="Sidebar penuh" aria-label="Sidebar penuh">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
                <button type="button" class="mode-btn" data-mode-toggle="compact" title="Icon saja" aria-label="Icon saja">
                    <i class="bi bi-layout-sidebar"></i>
                </button>
                <button type="button" class="mode-btn" data-mode-toggle="hidden" title="Sembunyikan sidebar" aria-label="Sembunyikan sidebar">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>

            <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none sidebar-close"
                data-sidebar-close aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-body">
        <div class="sidebar-section-title">Utama</div>
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}"
                    title="Dashboard">
                    <i class="bi bi-speedometer2 me-2"></i><span class="link-label hide-in-compact">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.events.index') }}"
                    class="nav-link {{ request()->routeIs('admin.events.index', 'admin.events.create', 'admin.events.edit', 'admin.events.show') ? 'active' : '' }}"
                    title="Event">
                    <i class="bi bi-calendar-event me-2"></i><span class="link-label hide-in-compact">Event</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.tutorial') }}"
                    class="nav-link {{ request()->routeIs('admin.tutorial') ? 'active' : '' }}"
                    title="Tutorial">
                    <i class="bi bi-book me-2"></i><span class="link-label hide-in-compact">Tutorial</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Manajemen Event</div>

        @if ($currentEventId)
            <ul class="nav flex-column gap-1">
                @foreach ($eventMenus as $menu)
                    <li class="nav-item">
                        <a href="{{ route($menu['route'], $currentEventId) }}"
                            class="nav-link {{ request()->routeIs($menu['route']) ? 'active' : '' }}"
                            title="{{ $menu['label'] }}">
                            <i class="bi {{ $menu['icon'] }} me-2"></i><span class="link-label hide-in-compact">{{ $menu['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <ul class="nav flex-column gap-1">
                @foreach ($eventMenus as $menu)
                    <li class="nav-item">
                        <span class="nav-link disabled" title="Buka salah satu event terlebih dahulu">
                            <i class="bi {{ $menu['icon'] }} me-2"></i><span class="link-label hide-in-compact">{{ $menu['label'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>
            <p class="small text-muted px-3 mt-2 mb-0 hide-in-compact">
                Pilih salah satu event untuk mengelola tier, add-on, voucher, dan pendaftar.
            </p>
        @endif
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <span class="small text-muted hide-in-compact">Tema</span>
            <x-theme-toggle variant="btn-outline-secondary" />
        </div>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 mb-2" title="Lihat Web">
            <i class="bi bi-box-arrow-up-right me-1"></i><span class="hide-in-compact">Lihat Web</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary w-100 mb-2" title="Edit Profil">
            <i class="bi bi-pencil-square me-1"></i><span class="hide-in-compact">Edit Profil</span>
        </a>
        <div class="border rounded p-2 mb-2 text-center">
            <div class="d-flex align-items-center justify-content-center gap-2">
                @if (Auth::check() && Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                        class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                @else
                    <i class="bi bi-person-circle fs-4"></i>
                @endif
                <div class="small text-start hide-in-compact">
                    <div class="fw-semibold">{{ Auth::user()->name ?? 'Guest' }}</div>
                    <div class="text-muted" style="font-size: .75rem;">{{ Auth::user()->email ?? '' }}</div>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="d-grid">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100" title="Logout">
                <i class="bi bi-box-arrow-right me-1"></i><span class="hide-in-compact">Logout</span>
            </button>
        </form>
    </div>
</aside>

@push('scripts')
<script>
(function () {
    var shell = document.getElementById('adminShell');
    var resize = document.getElementById('sidebarResizeHandle');
    if (!shell) return;

    var MODE_KEY = 'kafeinarts.sidebar.mode';
    var WIDTH_KEY = 'kafeinarts.sidebar.width';
    var MIN = 180, MAX = 360;

    function clamp(value) {
        value = parseInt(value, 10);
        if (isNaN(value)) value = 264;
        return Math.min(MAX, Math.max(MIN, value));
    }

    function applyMode(mode) {
        shell.setAttribute('data-sidebar-mode', mode);
        try { localStorage.setItem(MODE_KEY, mode); } catch (e) {}
        document.querySelectorAll('[data-mode-toggle]').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-mode-toggle') === mode);
        });
        if (resize) positionHandle();
    }

    function applyWidth(width) {
        var w = clamp(width);
        shell.style.setProperty('--sidebar-width', w + 'px');
        try { localStorage.setItem(WIDTH_KEY, String(w)); } catch (e) {}
    }

    function positionHandle() {
        if (!resize) return;
        resize.style.left = 'calc(var(--sidebar-width) - 4px)';
    }

    try {
        applyMode(localStorage.getItem(MODE_KEY) || 'full');
        applyWidth(localStorage.getItem(WIDTH_KEY));
    } catch (e) {
        applyMode('full');
        applyWidth(264);
    }

    document.querySelectorAll('[data-mode-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            applyMode(btn.getAttribute('data-mode-toggle'));
        });
    });

    if (resize) {
        var dragging = false;
        resize.addEventListener('mousedown', function (event) {
            dragging = true;
            resize.classList.add('dragging');
            event.preventDefault();
        });

        window.addEventListener('mousemove', function (event) {
            if (!dragging) return;
            var w = clamp(event.clientX);
            applyWidth(w);
            positionHandle();
        });

        window.addEventListener('mouseup', function () {
            dragging = false;
            if (resize) resize.classList.remove('dragging');
        });
    }
})();
</script>
@endpush

<div class="sidebar-resize-handle" id="sidebarResizeHandle" aria-hidden="true"></div>
<button type="button" class="sidebar-reveal-fab" data-mode-toggle="full" aria-label="Tampilkan sidebar" title="Tampilkan sidebar">
    <i class="bi bi-layout-sidebar-inset"></i>
</button>
<div class="sidebar-backdrop d-lg-none" data-sidebar-close hidden></div>
