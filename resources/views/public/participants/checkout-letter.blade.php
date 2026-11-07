@extends('layouts.app')

@section('title', 'Surat Checkout Event')

@push('styles')
    @include('tickets.checkout-letter-styles')
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
        <div>
            <h1 class="h4 mb-1">Surat Checkout Event</h1>
            <p class="text-muted small mb-0">
                {{ $event->name }} — {{ $registrant->full_name }}
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Cetak
            </button>
            <a href="{{ route('public.participants.checkout-letter.download', $registrant->participant_token) }}"
                class="btn btn-success">
                <i class="bi bi-file-earmark-pdf me-1"></i>Unduh PDF
            </a>
            <a href="{{ route('public.participants.ticket', $registrant->participant_token) }}"
                class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Tiket
            </a>
        </div>
    </div>

    @include('tickets.checkout-letter-sheet')
</div>
@endsection
