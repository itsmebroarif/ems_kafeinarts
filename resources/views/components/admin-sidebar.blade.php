@php
    $currentEvent = request()->route('event');
    $currentEventId = is_object($currentEvent) ? $currentEvent->id : $currentEvent;

    $eventMenus = [
        ['route' => 'admin.events.tiers.index', 'icon' => 'bi-ticket-detailed', 'label' => 'Tier Tiket'],
        ['route' => 'admin.events.addons.index', 'icon' => 'bi-bag-plus', 'label' => 'Add-on'],
        ['route' => 'admin.events.vouchers.index', 'icon' => 'bi-tag', 'label' => 'Voucher'],
        ['route' => 'admin.events.registrants.index', 'icon' => 'bi-people', 'label' => 'Pendaftar'],
    ];
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="brand d-flex align-items-center gap-2 text-decoration-none">
            <i class="bi bi-shield-check text-warning fs-5"></i>
            <span class="fw-bold">Kafeinarts</span>
        </a>
        <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none sidebar-close"
            data-sidebar-close aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
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
        <div class="small text-muted text-center">
            <i class="bi bi-person-circle me-1"></i>{{ Auth::check() ? Auth::user()->name : 'Admin' }}
        </div>
    </div>
</aside>

<div class="sidebar-backdrop d-lg-none" data-sidebar-close hidden></div>
