@extends('layouts.app')

@section('title', 'EMS Kafeinarts - Event Management System')

@section('content')
<div class="container py-4">
    <!-- Hero Section -->
    <div class="p-5 mb-5 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                    <i class="bi bi-stars me-1"></i> Kafeinarts Ticketing & EMS
                </span>
                <h1 class="display-5 fw-bold mb-3">Event Management System</h1>
                <p class="lead text-light opacity-75 mb-4">
                    Solusi terpadu manajemen event, alur pendaftaran peserta interaktif, serta tiket elektronik instan dengan validasi QR Code.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('public.events.index') }}" class="btn btn-warning btn-lg px-4 fw-semibold shadow-sm">
                        <i class="bi bi-calendar2-event me-2"></i>Jelajahi Event
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <i class="bi bi-ticket-detailed text-warning" style="font-size: 8rem; opacity: 0.85;"></i>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 bg-primary bg-opacity-10 text-primary rounded-3 mb-3">
                        <i class="bi bi-calendar-event fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Manajemen Event</h5>
                    <p class="text-muted small mb-0">Kelola master acara, tier harga tiket, merchandise add-on, voucher potongan, dan pantau kuota peserta.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 bg-success bg-opacity-10 text-success rounded-3 mb-3">
                        <i class="bi bi-people fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Pendaftaran Peserta</h5>
                    <p class="text-muted small mb-0">Wizard pendaftaran multi-step yang intuitif, pemilihan add-on fleksibel, dan checkout instan.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-inline-flex align-items-center justify-content-center p-3 bg-warning bg-opacity-10 text-dark rounded-3 mb-3">
                        <i class="bi bi-qr-code-scan fs-2"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tiket Elektronik</h5>
                    <p class="text-muted small mb-0">Unduh e-ticket dalam format PDF dengan QR Code unik yang siap diverifikasi saat check-in di lokasi acara.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Events Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body-secondary py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">
                <i class="bi bi-calendar-check text-primary me-2"></i>Event Tersedia
            </h5>
            <a href="{{ route('public.events.index') }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua Event <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse (App\Models\Event::where('is_published', true)->take(5)->get() as $event)
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center gap-3">
                            <x-event-avatar :event="$event" :size="44" />
                            <div>
                                <a href="{{ route('public.events.show', $event->slug) }}" class="fw-bold text-decoration-none text-body fs-6">
                                    {{ $event->name }}
                                </a>
                                <div class="small text-muted mt-1">
                                    <span class="me-3"><i class="bi bi-geo-alt me-1"></i>{{ $event->location ?? 'Online / Venue TBA' }}</span>
                                    <span><i class="bi bi-calendar3 me-1"></i>{{ $event->starts_at ? \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') : 'TBA' }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('public.events.show', $event->slug) }}" class="btn btn-sm btn-primary">
                            Beli Tiket <i class="bi bi-ticket-perforated ms-1"></i>
                        </a>
                    </li>
                @empty
                    <li class="list-group-item text-muted py-5 text-center">
                        <i class="bi bi-inbox fs-1 d-block text-secondary mb-2"></i>
                        Belum ada event yang dipublikasikan saat ini.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
