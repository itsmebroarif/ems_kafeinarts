@extends('layouts.app')

@section('title', 'Semua Event')

@php
    $currentParams = array_filter(request()->query(), fn ($v) => $v !== '');
    $buildUrl = function (array $overrides) use ($currentParams) {
        return route('public.events.index', array_filter(array_merge($currentParams, $overrides), fn ($v) => $v !== ''));
    };
@endphp

@section('content')
<div class="container py-3">

    {{-- Header halaman --}}
    <div class="text-center mb-4">
        <span class="badge text-bg-primary rounded-pill px-3 py-2 mb-2">
            <i class="bi bi-calendar-event me-1"></i>Event Tersedia : {{ $events->total() }}
        </span>
        <h1 class="h3 fw-bold mb-1">Daftar Event</h1>
        <p class="text-muted mb-0">Temukan event favoritmu dan amankan tiketnya sekarang.</p>
    </div>

    {{-- Pencarian --}}
    <form method="get" class="mb-4">
        @foreach (array_filter($filters, fn ($v) => $v !== '') as $name => $value)
            @if ($name !== 'search')
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endif
        @endforeach

        <div class="row justify-content-center g-2">
            <div class="col-sm-8 col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari nama atau lokasi event..."
                        value="{{ $filters['search'] ?? '' }}">
                    <button class="btn btn-primary" type="submit">Cari</button>
                    @if (($filters['search'] ?? '') !== '')
                        <a href="{{ $buildUrl(['search' => '']) }}"
                            class="btn btn-outline-secondary" title="Reset pencarian">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4">
        {{-- Sidebar filter di kiri --}}
        <div class="col-lg-3">
            <div class="sidebar-card sticky-top" style="top: 5.5rem; z-index: 10;">
                <div class="sidebar-header">
                    <div class="sidebar-header-icon">
                        <i class="bi bi-funnel-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-0">Filter Event</h6>
                        <small class="text-white-50">Pilih kategori & kriteria</small>
                    </div>
                </div>
                <div class="sidebar-body">
                    <div class="accordion accordion-flush" id="eventFilters">

                        {{-- Kategori --}}
                        <div class="accordion-item border-0">
                            <button class="accordion-button fw-semibold px-2 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategory" aria-expanded="true" aria-controls="collapseCategory">
                                <i class="bi bi-collection me-2"></i>Kategori
                            </button>
                            <div id="collapseCategory" class="accordion-collapse collapse show" data-bs-parent="#eventFilters">
                                <div class="accordion-body px-0 pt-0 pb-2">
                                    <ul class="nav flex-column gap-2">
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['category' => '', 'page' => '']) }}" class="sidebar-link {{ $activeCategory === '' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-grid"></i>
                                                    <span>Semua Event</span>
                                                </span>
                                                <span class="sidebar-badge">{{ $categories->sum('published_events_count') }}</span>
                                            </a>
                                        </li>

                                        @foreach ($categories as $category)
                                            @php
                                                $active = $activeCategory === $category->slug;
                                                $rgb = $category->rgb();
                                            @endphp
                                            <li class="nav-item">
                                                <a href="{{ $buildUrl(['category' => $category->slug, 'page' => '']) }}"
                                                    class="sidebar-link {{ $active ? 'active' : '' }}"
                                                    style="{{ $active
                                                        ? 'background: linear-gradient(135deg, rgb(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ') 0%, rgb(' . max(0, $rgb[0] - 30) . ',' . max(0, $rgb[1] - 30) . ',' . max(0, $rgb[2] - 30) . ') 100%); border-color: transparent;'
                                                        : '' }}">
                                                    <span class="d-flex align-items-center gap-2">
                                                        <span class="sidebar-dot" style="background-color: rgb({{ $rgb[0] }},{{ $rgb[1] }},{{ $rgb[2] }});"></span>
                                                        <span>{{ $category->name }}</span>
                                                    </span>
                                                    <span class="sidebar-badge">{{ $category->published_events_count }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Jenis Event --}}
                        <div class="accordion-item border-0">
                            <button class="accordion-button fw-semibold px-2 py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseType" aria-expanded="false" aria-controls="collapseType">
                                <i class="bi bi-trophy me-2"></i>Jenis Event
                            </button>
                            <div id="collapseType" class="accordion-collapse collapse" data-bs-parent="#eventFilters">
                                <div class="accordion-body px-0 pt-0 pb-2">
                                    <ul class="nav flex-column gap-2">
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['type' => '', 'page' => '']) }}" class="sidebar-link {{ $activeType === '' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-list-check"></i><span>Semua</span></span>
                                                <span class="sidebar-badge">{{ $typeCounts['kompetisi'] + $typeCounts['hiburan'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['type' => 'kompetisi', 'page' => '']) }}" class="sidebar-link {{ $activeType === 'kompetisi' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-trophy text-danger"></i><span>Kompetisi</span></span>
                                                <span class="sidebar-badge">{{ $typeCounts['kompetisi'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['type' => 'hiburan', 'page' => '']) }}" class="sidebar-link {{ $activeType === 'hiburan' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-stars text-info"></i><span>Event Hiburan</span></span>
                                                <span class="sidebar-badge">{{ $typeCounts['hiburan'] }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Lokasi --}}
                        @if ($cityCounts->isNotEmpty())
                            <div class="accordion-item border-0">
                                <button class="accordion-button fw-semibold px-2 py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCity" aria-expanded="false" aria-controls="collapseCity">
                                    <i class="bi bi-geo-alt me-2"></i>Lokasi
                                </button>
                                <div id="collapseCity" class="accordion-collapse collapse" data-bs-parent="#eventFilters">
                                    <div class="accordion-body px-0 pt-0 pb-2">
                                        <ul class="nav flex-column gap-2">
                                            <li class="nav-item">
                                                <a href="{{ $buildUrl(['city' => '', 'page' => '']) }}" class="sidebar-link {{ $activeCity === '' ? 'active' : '' }}">
                                                    <span class="d-flex align-items-center gap-2"><i class="bi bi-globe"></i><span>Semua Lokasi</span></span>
                                                    <span class="sidebar-badge">{{ $cityCounts->sum() }}</span>
                                                </a>
                                            </li>
                                            @foreach ($cityCounts as $city => $count)
                                                <li class="nav-item">
                                                    <a href="{{ $buildUrl(['city' => $city, 'page' => '']) }}" class="sidebar-link {{ $activeCity === $city ? 'active' : '' }}">
                                                        <span class="d-flex align-items-center gap-2"><i class="bi bi-building"></i><span>{{ $city }}</span></span>
                                                        <span class="sidebar-badge">{{ $count }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Harga --}}
                        <div class="accordion-item border-0">
                            <button class="accordion-button fw-semibold px-2 py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrice" aria-expanded="false" aria-controls="collapsePrice">
                                <i class="bi bi-cash-coin me-2"></i>Harga
                            </button>
                            <div id="collapsePrice" class="accordion-collapse collapse" data-bs-parent="#eventFilters">
                                <div class="accordion-body px-0 pt-0 pb-2">
                                    <ul class="nav flex-column gap-2">
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['price' => '', 'page' => '']) }}" class="sidebar-link {{ $activePrice === '' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-list"></i><span>Semua Harga</span></span>
                                                <span class="sidebar-badge">{{ $priceCounts['free'] + $priceCounts['paid'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['price' => 'free', 'page' => '']) }}" class="sidebar-link {{ $activePrice === 'free' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-gift text-success"></i><span>Gratis</span></span>
                                                <span class="sidebar-badge">{{ $priceCounts['free'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['price' => 'paid', 'page' => '']) }}" class="sidebar-link {{ $activePrice === 'paid' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-credit-card text-warning"></i><span>Berbayar</span></span>
                                                <span class="sidebar-badge">{{ $priceCounts['paid'] }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="accordion-item border-0">
                            <button class="accordion-button fw-semibold px-2 py-2 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDate" aria-expanded="false" aria-controls="collapseDate">
                                <i class="bi bi-calendar3 me-2"></i>Tanggal
                            </button>
                            <div id="collapseDate" class="accordion-collapse collapse" data-bs-parent="#eventFilters">
                                <div class="accordion-body px-0 pt-0 pb-2">
                                    <ul class="nav flex-column gap-2">
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['date' => '', 'page' => '']) }}" class="sidebar-link {{ $activeDate === '' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-calendar"></i><span>Semua Tanggal</span></span>
                                                <span class="sidebar-badge">{{ $dateCounts['today'] + $dateCounts['week'] + $dateCounts['month'] + $dateCounts['upcoming'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['date' => 'today', 'page' => '']) }}" class="sidebar-link {{ $activeDate === 'today' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-calendar-day"></i><span>Hari Ini</span></span>
                                                <span class="sidebar-badge">{{ $dateCounts['today'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['date' => 'week', 'page' => '']) }}" class="sidebar-link {{ $activeDate === 'week' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-calendar-week"></i><span>Minggu Ini</span></span>
                                                <span class="sidebar-badge">{{ $dateCounts['week'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['date' => 'month', 'page' => '']) }}" class="sidebar-link {{ $activeDate === 'month' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-calendar-month"></i><span>Bulan Ini</span></span>
                                                <span class="sidebar-badge">{{ $dateCounts['month'] }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ $buildUrl(['date' => 'upcoming', 'page' => '']) }}" class="sidebar-link {{ $activeDate === 'upcoming' ? 'active' : '' }}">
                                                <span class="d-flex align-items-center gap-2"><i class="bi bi-calendar-event"></i><span>Mendatang</span></span>
                                                <span class="sidebar-badge">{{ $dateCounts['upcoming'] }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar event di kanan --}}
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse ($events as $event)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden event-card">
                            <div class="position-relative">
                                <x-event-banner :event="$event" :height="150" />

                                <div class="position-absolute top-0 start-0 m-3 d-flex flex-column align-items-start gap-1">
                                    <x-event-category-badge :event="$event" />
                                    <x-event-type-badge :event="$event" />
                                </div>

                                <div class="position-absolute start-0 bottom-0 m-3">
                                    <x-event-avatar :event="$event" :size="54" />
                                </div>
                            </div>

                            <div class="card-body pt-4">
                                <h5 class="card-title fw-bold mb-1">
                                    <a href="{{ route('public.events.show', $event->slug) }}" class="text-decoration-none text-body">
                                        {{ $event->name }}
                                    </a>
                                </h5>

                                <div class="small text-muted mb-2 d-flex flex-wrap gap-3">
                                    <span><i class="bi bi-geo-alt me-1"></i>{{ $event->location ?? 'Online' }}</span>
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') }}
                                    </span>
                                </div>

                                <p class="card-text text-muted small mb-2">
                                    {{ \Illuminate\Support\Str::limit(strip_tags((string) $event->description), 110) }}
                                </p>
                            </div>

                            <div class="card-footer bg-transparent border-0 pt-0 d-flex align-items-center justify-content-between">
                                <span class="small text-muted">
                                    <i class="bi bi-people me-1"></i>{{ $event->registrants_count }} pendaftar
                                </span>
                                <a href="{{ route('public.events.show', $event->slug) }}" class="btn btn-sm btn-primary">
                                    Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-calendar-x fs-1 text-secondary d-block mb-3"></i>
                                <h5 class="fw-bold mb-2">Belum ada event</h5>
                                <p class="text-muted mx-auto mb-4" style="max-width: 460px;">
                                    @if ($activeCategory !== '' || ($filters['search'] ?? '') !== '' || $activeType !== '' || $activeCity !== '' || $activePrice !== '' || $activeDate !== '')
                                        Tidak ada event yang cocok dengan pencarian atau filter tersebut.
                                        Coba ubah filternya.
                                    @else
                                        Belum ada event yang dipublikasikan saat ini. Masuk ke panel admin
                                        untuk menambahkan event baru.
                                    @endif
                                </p>

                                @if ($activeCategory !== '' || ($filters['search'] ?? '') !== '' || $activeType !== '' || $activeCity !== '' || $activePrice !== '' || $activeDate !== '')
                                    <a href="{{ route('public.events.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah Event
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($events->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .sidebar-card {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12);
        background: var(--bs-body-bg);
        border: 1px solid rgba(0, 0, 0, .08);
    }

    .sidebar-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: .8rem;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
    }

    .sidebar-header-icon {
        width: 42px;
        height: 42px;
        border-radius: .75rem;
        background: rgba(255, 255, 255, .12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffd700;
        font-size: 1.25rem;
    }

    .sidebar-body {
        padding: .9rem;
        background: var(--bs-body-bg);
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        padding: .65rem .75rem;
        border-radius: .65rem;
        text-decoration: none;
        color: var(--bs-body-color);
        border: 1px solid transparent;
        transition: all .15s ease;
        font-size: .92rem;
        font-weight: 500;
    }

    .sidebar-link:hover {
        background-color: var(--bs-tertiary-bg);
        border-color: var(--bs-border-color-translucent);
        transform: translateX(4px);
    }

    .sidebar-link.active {
        color: #fff;
        font-weight: 600;
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        border-color: transparent;
        box-shadow: 0 .25rem .6rem rgba(0, 0, 0, .25);
    }

    .sidebar-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        opacity: .9;
    }

    .sidebar-badge {
        min-width: 2rem;
        padding: .2rem .55rem;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 700;
        text-align: center;
        background: var(--bs-tertiary-bg);
        color: var(--bs-body-color);
    }

    .sidebar-link.active .sidebar-badge {
        background: rgba(255, 255, 255, .25);
        color: #fff;
    }

    .accordion-button {
        background-color: transparent;
        box-shadow: none;
        padding: .7rem .5rem;
        font-size: .9rem;
        color: var(--bs-body-color);
    }

    .accordion-button:not(.collapsed) {
        color: var(--bs-primary);
        background-color: var(--bs-tertiary-bg);
    }

    .accordion-button:focus {
        box-shadow: none;
    }

    .accordion-button::after {
        width: 1rem;
        height: 1rem;
        background-size: 1rem;
    }
</style>
@endsection