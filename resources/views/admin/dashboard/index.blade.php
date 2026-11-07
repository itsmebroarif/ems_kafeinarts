@extends('layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
    <style>
        .dash-hero {
            position: relative;
            overflow: hidden;
            border: 0;
            border-radius: 1.25rem;
            background: linear-gradient(115deg, #4f46e5 0%, #7c3aed 48%, #db2777 100%);
            box-shadow: 0 .85rem 2rem -.85rem rgba(79, 70, 229, .7);
            color: #fff;
        }

        .dash-hero .hero-blob {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
            pointer-events: none;
        }

        .dash-card {
            border: 0;
            border-radius: 1.1rem;
            box-shadow: 0 .4rem 1.4rem -.6rem rgba(15, 23, 42, .18);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .dash-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 .75rem 1.9rem -.6rem rgba(15, 23, 42, .25);
        }

        [data-bs-theme="dark"] .dash-card {
            box-shadow: 0 .4rem 1.4rem -.6rem rgba(0, 0, 0, .65);
        }

        [data-bs-theme="dark"] .dash-card:hover {
            box-shadow: 0 .75rem 1.9rem -.6rem rgba(0, 0, 0, .75);
        }

        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(var(--accent-rgb), 1), rgba(var(--accent-rgb), .35));
        }

        .stat-icon {
            width: 3.25rem;
            height: 3.25rem;
            flex-shrink: 0;
            border-radius: .9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            background-color: rgba(var(--accent-rgb), .14);
            color: rgb(var(--accent-rgb));
        }

        .stat-meta {
            min-width: 0;
        }

        .stat-label {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--bs-secondary-color);
        }

        .stat-value {
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -.02em;
            line-height: 1.2;
            color: var(--bs-body-color);
        }

        .stat-hint {
            font-size: .78rem;
        }

        .revenue-card {
            border: 0;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, #059669 0%, #10b981 55%, #34d399 100%);
            color: #fff;
            box-shadow: 0 .75rem 1.8rem -.85rem rgba(16, 185, 129, .75);
        }

        .revenue-card .revenue-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .85;
        }

        .revenue-card .revenue-value {
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .progress-thin {
            height: .55rem;
            border-radius: 999px;
            background-color: var(--bs-secondary-bg);
        }

        .quick-action {
            display: flex;
            align-items: center;
            gap: .8rem;
            padding: .8rem .9rem;
            border: 1px solid var(--bs-border-color);
            border-radius: .85rem;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            text-decoration: none;
            transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .quick-action:hover {
            transform: translateY(-2px);
            border-color: rgba(var(--accent-rgb), .55);
            box-shadow: 0 .5rem 1.25rem -.75rem rgba(var(--accent-rgb), .55);
            color: var(--bs-body-color);
        }

        .quick-action .quick-icon {
            width: 2.5rem;
            height: 2.5rem;
            flex-shrink: 0;
            border-radius: .7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            background-color: rgba(var(--accent-rgb), .14);
            color: rgb(var(--accent-rgb));
        }

        .quick-action .quick-title {
            font-size: .9rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .quick-action .quick-sub {
            font-size: .75rem;
            color: var(--bs-secondary-color);
        }

        .dash-table thead th {
            border-bottom-width: 1px;
        }

        .dash-table tbody tr:last-child td {
            border-bottom-width: 0;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Hero --}}
    <div class="dash-hero p-4 p-md-5 mb-4">
        <span class="hero-blob" style="width: 240px; height: 240px; top: -110px; right: -60px;"></span>
        <span class="hero-blob" style="width: 150px; height: 150px; bottom: -80px; right: 140px;"></span>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative">
            <div>
                <span class="badge text-bg-light mb-2 px-3 py-2 rounded-pill">
                    <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                </span>
                <h1 class="h3 fw-bold mb-1">Selamat datang kembali 👋</h1>
                <p class="mb-0 opacity-75">
                    Pantau event, pendaftar, pembayaran, dan voucher dalam satu tempat.
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-light fw-semibold">
                    <i class="bi bi-calendar-event me-1"></i>Kelola Event
                </a>
                <a href="{{ route('admin.events.create') }}" class="btn btn-warning fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i>Buat Event
                </a>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-4">
            <div class="card dash-card stat-card h-100" style="--accent-rgb: 13, 110, 253;">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon"><i class="bi bi-calendar-event"></i></span>
                    <div class="stat-meta">
                        <div class="stat-label">Total Event</div>
                        <div class="stat-value">{{ $metrics['events'] }}</div>
                        <div class="stat-hint text-muted">{{ $metrics['published_events'] }} terpublikasi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card dash-card stat-card h-100" style="--accent-rgb: 13, 202, 240;">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon"><i class="bi bi-people"></i></span>
                    <div class="stat-meta">
                        <div class="stat-label">Total Pendaftar</div>
                        <div class="stat-value">{{ $metrics['total'] }}</div>
                        <div class="stat-hint text-muted">Semua event</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card dash-card stat-card h-100" style="--accent-rgb: 25, 135, 84;">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon"><i class="bi bi-patch-check"></i></span>
                    <div class="stat-meta">
                        <div class="stat-label">Pembayaran Lunas</div>
                        <div class="stat-value">{{ $metrics['paid'] }}</div>
                        <div class="stat-hint text-muted">Terbayar penuh</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card dash-card stat-card h-100" style="--accent-rgb: 255, 193, 7;">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon"><i class="bi bi-hourglass-split"></i></span>
                    <div class="stat-meta">
                        <div class="stat-label">Menunggu Bayar</div>
                        <div class="stat-value">{{ $metrics['pending'] }}</div>
                        <div class="stat-hint text-muted">Perlu ditindaklanjuti</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card dash-card stat-card h-100" style="--accent-rgb: 111, 66, 193;">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon"><i class="bi bi-person-check"></i></span>
                    <div class="stat-meta">
                        <div class="stat-label">Terkonfirmasi</div>
                        <div class="stat-value">{{ $metrics['confirmed'] }}</div>
                        <div class="stat-hint text-muted">Status confirmed</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="card dash-card stat-card h-100" style="--accent-rgb: 214, 51, 134;">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon"><i class="bi bi-ticket-perforated"></i></span>
                    <div class="stat-meta">
                        <div class="stat-label">Voucher Terpakai</div>
                        <div class="stat-value">{{ $metrics['vouchers_used'] }}</div>
                        <div class="stat-hint text-muted">Kode voucher digunakan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Daftar event --}}
        <div class="col-lg-8">
            <div class="card dash-card h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-1 fw-bold"><i class="bi bi-table text-primary me-2"></i>Event Terbaru</h5>
                        <div class="small text-muted">Ringkasan pendaftar dan pembayaran per event</div>
                    </div>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="card-body px-4 pb-4 pt-2">
                    @if ($events->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-secondary d-block mb-2"></i>
                            <p class="text-muted mb-3">Belum ada event.</p>
                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i>Buat Event Pertama
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 dash-table">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th class="text-center">Pendaftar</th>
                                        <th class="text-center">Lunas</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($events as $event)
                                        @php
                                            $totalRegistrants = (int) $event->registrants_count;
                                            $paidRegistrants = (int) $event->paid_registrants_count;
                                            $paidPercent = $totalRegistrants > 0
                                                ? (int) round(($paidRegistrants / $totalRegistrants) * 100)
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <x-event-avatar :event="$event" :size="40" />
                                                    <div class="flex-grow-1" style="min-width: 0;">
                                                        <a href="{{ route('admin.events.edit', $event->id) }}"
                                                            class="fw-semibold text-decoration-none d-block text-truncate"
                                                            style="max-width: 220px;">
                                                            {{ $event->name }}
                                                        </a>
                                                        <span class="small text-muted">
                                                            <i class="bi bi-geo-alt me-1"></i>{{ $event->location ?? 'Online' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold">{{ $totalRegistrants }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-semibold text-success">{{ $paidRegistrants }}</span>
                                                <div class="progress progress-thin mt-1" style="min-width: 70px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $paidPercent }}%"
                                                        aria-valuenow="{{ $paidPercent }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($event->is_published)
                                                    <span class="badge rounded-pill text-bg-success">Terpublikasi</span>
                                                @else
                                                    <span class="badge rounded-pill text-bg-secondary">Draf</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.events.registrants.index', $event->id) }}"
                                                        class="btn btn-outline-info"
                                                        title="Pendaftar"><i class="bi bi-people"></i></a>
                                                    <a href="{{ route('admin.events.tiers.index', $event->id) }}"
                                                        class="btn btn-outline-primary"
                                                        title="Tier Tiket"><i class="bi bi-ticket-detailed"></i></a>
                                                    <a href="{{ route('admin.events.edit', $event->id) }}"
                                                        class="btn btn-outline-secondary"
                                                        title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @if ($events->hasPages())
                    <div class="card-footer bg-transparent border-0 px-4 pb-4">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Panel kanan --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-4">

                <div class="revenue-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="revenue-label">Nilai Pendaftaran</span>
                        <i class="bi bi-wallet2 fs-4 opacity-75"></i>
                    </div>
                    <div class="revenue-value">Rp {{ number_format($metrics['gross'], 0, ',', '.') }}</div>
                    <div class="small opacity-75 mt-1">
                        Rp {{ number_format($metrics['revenue'], 0, ',', '.') }} lunas dari
                        {{ $metrics['total'] }} pendaftaran
                    </div>
                </div>

                <div class="card dash-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up text-info me-2"></i>Status Pendaftaran</h5>

                        @php
                            $denominator = max(1, (int) $metrics['total']);
                            $segments = [
                                ['label' => 'Lunas', 'value' => $metrics['paid'], 'class' => 'bg-success'],
                                ['label' => 'Menunggu', 'value' => $metrics['pending'], 'class' => 'bg-warning'],
                                ['label' => 'Terkonfirmasi', 'value' => $metrics['confirmed'], 'class' => 'bg-info'],
                            ];
                        @endphp

                        @foreach ($segments as $segment)
                            @php
                                $percent = (int) round(($segment['value'] / $denominator) * 100);
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold">{{ $segment['label'] }}</span>
                                    <span class="text-muted">{{ $segment['value'] }} ({{ $percent }}%)</span>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar {{ $segment['class'] }}" role="progressbar"
                                        style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endforeach

                        @if ((int) $metrics['total'] === 0)
                            <p class="small text-muted mb-0">Belum ada pendaftar yang masuk.</p>
                        @endif
                    </div>
                </div>

                <div class="card dash-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-lightning-charge text-warning me-2"></i>Akses Cepat</h5>

                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('admin.events.create') }}" class="quick-action"
                                style="--accent-rgb: 13, 110, 253;">
                                <span class="quick-icon"><i class="bi bi-plus-lg"></i></span>
                                <span>
                                    <span class="quick-title d-block">Buat Event</span>
                                    <span class="quick-sub">Mulai event baru dengan wizard</span>
                                </span>
                            </a>

                            <a href="{{ route('admin.events.index') }}" class="quick-action"
                                style="--accent-rgb: 25, 135, 84;">
                                <span class="quick-icon"><i class="bi bi-calendar2-check"></i></span>
                                <span>
                                    <span class="quick-title d-block">Kelola Event</span>
                                    <span class="quick-sub">Tier, add-on, voucher, pendaftar</span>
                                </span>
                            </a>

                            <a href="{{ route('home') }}" target="_blank" class="quick-action"
                                style="--accent-rgb: 111, 66, 193;">
                                <span class="quick-icon"><i class="bi bi-box-arrow-up-right"></i></span>
                                <span>
                                    <span class="quick-title d-block">Lihat Situs Publik</span>
                                    <span class="quick-sub">Pratinjau halaman event</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
