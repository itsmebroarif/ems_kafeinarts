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

<aside class="admin-sidebar" id="adminSidebar" aria-label="Menu navigasi">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="brand d-flex align-items-center gap-2 text-decoration-none">
            <i class="bi bi-shield-check text-warning fs-5"></i>
            <span class="fw-bold">Kafeinarts</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="sidebar-hide-toggle d-none d-lg-inline-flex" id="sidebarHideToggle"
                title="Sembunyikan sidebar" aria-label="Sembunyikan sidebar" aria-pressed="false">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>

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
                    class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.events.index') }}"
                    class="nav-link {{ request()->routeIs('admin.events.index', 'admin.events.create', 'admin.events.edit', 'admin.events.show') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event me-2"></i>Event
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.tutorial') }}"
                    class="nav-link {{ request()->routeIs('admin.tutorial') ? 'active' : '' }}">
                    <i class="bi bi-book me-2"></i>Tutorial
                </a>
            </li>
        </ul>

        <div class="sidebar-section-title">Manajemen Event</div>

        @if ($currentEventId)
            <ul class="nav flex-column gap-1">
                @foreach ($eventMenus as $menu)
                    <li class="nav-item">
                        <a href="{{ route($menu['route'], $currentEventId) }}"
                            class="nav-link {{ request()->routeIs($menu['route']) ? 'active' : '' }}">
                            <i class="bi {{ $menu['icon'] }} me-2"></i>{{ $menu['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <ul class="nav flex-column gap-1">
                @foreach ($eventMenus as $menu)
                    <li class="nav-item">
                        <span class="nav-link disabled" title="Buka salah satu event terlebih dahulu">
                            <i class="bi {{ $menu['icon'] }} me-2"></i>{{ $menu['label'] }}
                        </span>
                    </li>
                @endforeach
            </ul>
            <p class="small text-muted px-3 mt-2 mb-0">
                Pilih salah satu event untuk mengelola tier, add-on, voucher, dan pendaftar.
            </p>
        @endif
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <span class="small text-muted">Tema</span>
            <x-theme-toggle variant="btn-outline-secondary" />
        </div>
        <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 mb-2">
            <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Web
        </a>
        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
            <i class="bi bi-pencil-square me-1"></i>Edit Profil
        </a>
        <div class="border rounded p-2 mb-2 text-center">
            <div class="d-flex align-items-center justify-content-center gap-2">
                @if (Auth::check() && Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                        class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                @else
                    <i class="bi bi-person-circle fs-4"></i>
                @endif
                <div class="small text-start">
                    <div class="fw-semibold">{{ Auth::user()->name ?? 'Guest' }}</div>
                    <div class="text-muted" style="font-size: .75rem;">{{ Auth::user()->email ?? '' }}</div>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="d-grid">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</aside>

@push('scripts')
<script>
(function () {
    var shell = document.getElementById('adminShell');
    if (!shell) return;

    var HIDDEN_KEY = 'kafeinarts.sidebar.hidden';

    function isHidden() {
        try { return localStorage.getItem(HIDDEN_KEY) === '1'; } catch (e) { return false; }
    }

    function setHidden(hidden) {
        shell.classList.toggle('sidebar-hidden', hidden);
        try { localStorage.setItem(HIDDEN_KEY, hidden ? '1' : '0'); } catch (e) {}

        document.querySelectorAll('.sidebar-hide-toggle').forEach(function (btn) {
            btn.setAttribute('aria-pressed', hidden ? 'true' : 'false');
            btn.setAttribute('title', hidden ? 'Tampilkan sidebar' : 'Sembunyikan sidebar');
            var icon = btn.querySelector('i');
            if (icon) {
                icon.className = hidden ? 'bi bi-layout-sidebar-inset' : 'bi bi-layout-sidebar-inset-reverse';
            }
        });
    }

    // Terapkan state tersimpan saat halaman dimuat.
    setHidden(isHidden());

    // Tombol toggle di header sidebar & FAB reveal.
    document.querySelectorAll('.sidebar-hide-toggle, .sidebar-reveal-fab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setHidden(!shell.classList.contains('sidebar-hidden'));
        });
    });
})();
</script>
@endpush

<button type="button" class="sidebar-reveal-fab" aria-label="Tampilkan sidebar" title="Tampilkan sidebar">
    <i class="bi bi-layout-sidebar-inset"></i>
</button>
<div class="sidebar-backdrop d-lg-none" data-sidebar-close hidden></div>
