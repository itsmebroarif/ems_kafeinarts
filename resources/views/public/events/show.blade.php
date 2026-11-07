@extends('layouts.app')

@section('title', $event->name)

@section('content')
<div class="container py-2">
    <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <x-event-banner :event="$event" :height="260" />

        <div class="card-body">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <x-event-avatar :event="$event" :size="76" />

                <div style="min-width: 240px; flex: 1;">
                    <div class="mb-2">
                        <x-event-category-badge :event="$event" />
                        <x-event-type-badge :event="$event" class="ms-1" />
                    </div>
                    <h1 class="h3 fw-bold mb-1">{{ $event->name }}</h1>
                    <div class="d-flex flex-wrap gap-3 small text-muted mb-0">
                        <span><i class="bi bi-geo-alt me-1"></i>{{ $event->location ?? 'Online' }}</span>
                        <span>
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') }}
                            @if ($event->ends_at)
                                s/d {{ \Illuminate\Support\Carbon::parse($event->ends_at)->format('d M Y H:i') }}
                            @endif
                        </span>
                    </div>
                </div>

                <div>
                    @if ($event->is_published)
                        <span class="badge rounded-pill text-bg-success">
                            <i class="bi bi-broadcast me-1"></i>Tersedia
                        </span>
                    @else
                        <span class="badge rounded-pill text-bg-secondary">Draf</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-file-text text-primary me-2"></i>Tentang Event</h5>

                    @if ($event->description)
                        <div class="event-description">{!! $event->description !!}</div>
                    @else
                        <p class="text-muted mb-0">Belum ada deskripsi untuk event ini.</p>
                    @endif

                    @if ($event->venue_details)
                        <hr>
                        <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt text-danger me-2"></i>Detail Venue</h6>
                        <p class="mb-0 text-muted">{!! nl2br(e($event->venue_details)) !!}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-ticket-perforated text-warning me-2"></i>Pilih Tier</h5>

                    @if ($tiers->isEmpty())
                        <p class="text-muted small mb-0">
                            Belum ada tier tiket yang tersedia untuk event ini.
                        </p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($tiers as $tier)
                                <li class="list-group-item px-0">
                                    <a href="{{ route('public.events.stage', $event->slug) }}?tier_id={{ $tier->id }}"
                                        class="btn btn-primary w-100">
                                        {{ $tier->name }} — Rp {{ number_format($tier->price, 0, ',', '.') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
