@extends('layouts.app')

@section('title', 'Tiket Peserta')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h1 class="h3">Tiket Peserta</h1>
                        <p class="text-muted">Kode tiket: {{ $registrant->participant_token }}</p>
                    </div>

                    <div class="card border-0 shadow-sm mb-4" style="padding: 24px;">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                <div class="bg-light rounded-pill p-3">
                                    @if ($registrant->ticketItems->isNotEmpty() && $registrant->ticketItems->first()->qr_code)
                                        <img src="{{ $registrant->ticketItems->first()->qr_code }}" alt="QR Code"
                                            style="max-width: 140px;">
                                    @else
                                        <span class="text-muted small">QR Code belum tersedia</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h5 class="mb-0">{{ $event->name }}</h5>
                                <p class="text-muted small mb-2">
                                    {{ $event->location ?? 'Online' }}
                                    @if ($event->starts_at)
                                        · {{ \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') }}
                                    @endif
                                </p>

                                <div class="row text-start">
                                    <div class="col-6"><strong>Nama:</strong> {{ $registrant->full_name }}</div>
                                    <div class="col-6"><strong>Email:</strong> {{ $registrant->email }}</div>
                                </div>
                                <div class="row text-start mt-2">
                                    <div class="col-6"><strong>Tier:</strong> {{ $registrant->ticketTier->name }}</div>
                                    <div class="col-6"><strong>Status:</strong>
                                        {{ $registrant->is_paid ? 'Lunas' : $registrant->status }}</div>
                                </div>
                                <div class="row text-start mt-2">
                                    <div class="col-6"><strong>Bukti:</strong> {{ $registrant->exchange_id ?? '-' }}</div>
                                    <div class="col-6"><strong>Terbayarkan:</strong>
                                        Rp {{ number_format((float) $registrant->total, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center flex-wrap">
                        @if (! $registrant->is_paid)
                            <a href="{{ route('public.participants.payment', $registrant->participant_token) }}"
                                class="btn btn-success btn-lg">
                                <i class="bi bi-credit-card me-1"></i>Bayar Sekarang
                            </a>
                        @else
                            <span class="btn btn-success disabled"><i class="bi bi-check-circle me-1"></i>Lunas</span>
                        @endif
                        <a href="{{ route('public.participants.ticket.print', $registrant->participant_token) }}"
                            class="btn btn-primary">
                            <i class="bi bi-printer me-1"></i>Cetak Tiket
                        </a>
                        <a href="{{ route('public.participants.ticket.download', $registrant->participant_token) }}"
                            class="btn btn-success">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Unduh Tiket (PDF)
                        </a>
                        <a href="{{ route('public.participants.checkout-letter', $registrant->participant_token) }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-envelope-paper me-1"></i>Surat Checkout Event
                        </a>
                        <a href="{{ route('public.events.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
