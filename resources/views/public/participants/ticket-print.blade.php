@extends('layouts.app')

@section('title', 'Cetak Tiket')

@php
    $seed = (string) ($event->id ?? $event->name ?? 'event');
    $qrImage = $ticket?->qr_code;
@endphp

@push('styles')
<style>
    .ticket-print-sheet {
        max-width: 820px;
        margin: 0 auto;
        background: #fff;
        color: #1a1a1a;
        border: 2px solid #1a1a1a;
        border-radius: 10px;
        padding: 28px;
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 13px;
    }

    .ticket-print-sheet .tp-header {
        display: flex;
        align-items: center;
        gap: 16px;
        border-bottom: 2px solid #1a1a1a;
        padding-bottom: 14px;
        margin-bottom: 16px;
    }

    .ticket-print-sheet .tp-logo {
        width: 72px;
        height: 72px;
        flex: 0 0 72px;
        border-radius: 50%;
        background: #2d6a4f;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: bold;
    }

    .ticket-print-sheet .tp-event {
        font-size: 20px;
        font-weight: bold;
        margin: 0 0 4px;
    }

    .ticket-print-sheet .tp-meta {
        color: #555;
        font-size: 13px;
        margin: 0;
    }

    .ticket-print-sheet .tp-body {
        display: flex;
        gap: 24px;
    }

    .ticket-print-sheet .tp-qr {
        flex: 0 0 200px;
        border: 2px dashed #1a1a1a;
        border-radius: 8px;
        padding: 14px;
        text-align: center;
        background: #f8faf9;
    }

    .ticket-print-sheet .tp-qr img {
        max-width: 170px;
        max-height: 170px;
    }

    .ticket-print-sheet .tp-qr .tp-qr-label {
        font-size: 11px;
        color: #555;
        margin-top: 8px;
    }

    .ticket-print-sheet .tp-details {
        flex: 1;
    }

    .ticket-print-sheet .tp-row {
        display: flex;
        gap: 12px;
        padding: 7px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .ticket-print-sheet .tp-row:last-child {
        border-bottom: none;
    }

    .ticket-print-sheet .tp-label {
        width: 150px;
        font-weight: bold;
        color: #555;
    }

    .ticket-print-sheet .tp-value {
        flex: 1;
    }

    .ticket-print-sheet .tp-footer {
        margin-top: 20px;
        padding-top: 12px;
        border-top: 2px solid #1a1a1a;
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #555;
    }

    .ticket-print-sheet .tp-total {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 2px solid #1a1a1a;
        display: flex;
        justify-content: space-between;
        font-size: 17px;
        font-weight: bold;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .ticket-print-sheet,
        .ticket-print-sheet * {
            visibility: visible;
        }

        .ticket-print-sheet {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
            border: none;
            border-radius: 0;
            padding: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
        <div>
            <h1 class="h4 mb-1">Cetak Tiket</h1>
            <p class="text-muted small mb-0">
                Kode tiket: <strong>{{ $registrant->participant_token }}</strong>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Cetak
            </button>
            <a href="{{ route('public.participants.ticket.download', $registrant->participant_token) }}"
                class="btn btn-success">
                <i class="bi bi-file-earmark-pdf me-1"></i>Unduh Tiket (PDF)
            </a>
            <a href="{{ route('public.participants.checkout-letter', $registrant->participant_token) }}"
                class="btn btn-outline-primary">
                <i class="bi bi-envelope-paper me-1"></i>Surat Checkout
            </a>
            <a href="{{ route('public.participants.ticket', $registrant->participant_token) }}"
                class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="ticket-print-sheet">
        <div class="tp-header">
            <div class="tp-logo">K</div>
            <div>
                <p class="tp-event">{{ $event->name }}</p>
                <p class="tp-meta">
                    {{ $event->location ?? 'Online' }}
                    @if ($event->starts_at)
                        · {{ \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="tp-body">
            <div class="tp-qr">
                @if ($qrImage)
                    <img src="{{ $qrImage }}" alt="QR Code Tiket">
                    <div class="tp-qr-label">Scan di pintu masuk</div>
                @else
                    <div class="tp-qr-label">QR Code belum tersedia</div>
                @endif
            </div>

            <div class="tp-details">
                <div class="tp-row">
                    <div class="tp-label">Peserta</div>
                    <div class="tp-value">{{ $registrant->full_name }}</div>
                </div>
                <div class="tp-row">
                    <div class="tp-label">Email</div>
                    <div class="tp-value">{{ $registrant->email }}</div>
                </div>
                <div class="tp-row">
                    <div class="tp-label">Tier Tiket</div>
                    <div class="tp-value">{{ $tier?->name ?? '-' }}</div>
                </div>
                <div class="tp-row">
                    <div class="tp-label">Kode Tiket</div>
                    <div class="tp-value">{{ $ticket?->code ?? $registrant->participant_token }}</div>
                </div>
                <div class="tp-row">
                    <div class="tp-label">Status</div>
                    <div class="tp-value">
                        {{ $registrant->is_paid ? 'LUNAS' : strtoupper((string) $registrant->status) }}
                    </div>
                </div>
                <div class="tp-row">
                    <div class="tp-label">Bukti Bayar</div>
                    <div class="tp-value">{{ $registrant->exchange_id ?? '-' }}</div>
                </div>

                <div class="tp-total">
                    <span>Total</span>
                    <span>Rp {{ number_format((float) $registrant->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="tp-footer">
            <span>Event Organizer: {{ config('app.name') }}</span>
            <span>ID Peserta: {{ $registrant->participant_token }}</span>
        </div>
    </div>
</div>
@endsection
