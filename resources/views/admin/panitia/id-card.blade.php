@extends('layouts.app')

@section('title', 'ID Card — ' . $member->user->name)

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
        <div>
            <a href="{{ route('admin.events.panitia.index', $event->id) }}" class="small text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Panitia
            </a>
            <h1 class="h4 fw-bold mb-0 mt-1">ID Card Panitia</h1>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Cetak ID Card
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-center">
        <div class="id-card">
            <div class="id-card-header">
                <div class="id-card-brand">Kafeinarts EMS</div>
                <div class="id-card-event">{{ $event->name }}</div>
                <div class="id-card-role-label">PANITIA</div>
            </div>

            <div class="id-card-body">
                <div class="id-card-avatar">
                    @if ($member->user->avatar_url)
                        <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}">
                    @else
                        <span>{{ $member->user->initials() }}</span>
                    @endif
                </div>

                <h2 class="id-card-name">{{ $member->user->name }}</h2>
                <div class="id-card-role">{{ \App\Enums\PanitiaRole::label($member->role) }}</div>

                <div class="id-card-divider"></div>

                <div class="id-card-qr">
                    <img src="{{ $qr }}" alt="QR Panitia">
                </div>
                <div class="id-card-id">{{ $cardId }}</div>
                <div class="id-card-caption">Scan untuk verifikasi kepanitiaan</div>
            </div>

            <div class="id-card-footer">
                <div>{{ strtoupper(config('app.name')) }}</div>
                <div class="id-card-valid">
                    Berlaku s/d {{ optional($event->ends_at)->format('d M Y') ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .id-card {
        width: 86mm;
        min-height: 135mm;
        background: linear-gradient(160deg, #141e30 0%, #243b55 100%);
        border-radius: 12mm;
        color: #fff;
        overflow: hidden;
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .25);
        display: flex;
        flex-direction: column;
        text-align: center;
        font-family: 'Helvetica', 'Arial', sans-serif;
    }

    .id-card-header {
        padding: 6mm 5mm 4mm;
        background: rgba(255, 255, 255, .06);
        border-bottom: 1px solid rgba(255, 255, 255, .15);
    }

    .id-card-brand {
        font-size: 8pt;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #ffd700;
        font-weight: 700;
    }

    .id-card-event {
        font-size: 10pt;
        font-weight: 600;
        margin-top: 1mm;
        line-height: 1.3;
    }

    .id-card-role-label {
        display: inline-block;
        margin-top: 2mm;
        padding: 1mm 4mm;
        border: 1px solid rgba(255, 255, 255, .55);
        border-radius: 999px;
        font-size: 7pt;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .85);
    }

    .id-card-body {
        padding: 4mm 5mm;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .id-card-avatar {
        width: 30mm;
        height: 30mm;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 3mm;
        border: 2.5mm solid rgba(255, 255, 255, .15);
        display: flex;
        align-items: center;
        justify-content: center;
        background: #2f4a66;
    }

    .id-card-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .id-card-avatar span {
        font-size: 26pt;
        font-weight: 700;
        color: #ffd700;
    }

    .id-card-name {
        font-size: 13pt;
        font-weight: 700;
        margin: 0;
    }

    .id-card-role {
        display: inline-block;
        margin-top: 1mm;
        padding: 1mm 4mm;
        border-radius: 999px;
        background: #ffd700;
        color: #141e30;
        font-size: 8pt;
        font-weight: 800;
        letter-spacing: .5px;
    }

    .id-card-divider {
        width: 40mm;
        height: 1px;
        background: rgba(255, 255, 255, .2);
        margin: 3mm auto;
    }

    .id-card-qr {
        background: #fff;
        padding: 2mm;
        border-radius: 3mm;
        line-height: 0;
    }

    .id-card-qr img {
        width: 22mm;
        height: 22mm;
    }

    .id-card-id {
        font-size: 8pt;
        letter-spacing: 1.5px;
        font-weight: 700;
        margin-top: 2mm;
        color: rgba(255, 255, 255, .9);
    }

    .id-card-caption {
        font-size: 6pt;
        color: rgba(255, 255, 255, .5);
        margin-top: 1mm;
    }

    .id-card-footer {
        padding: 3mm 5mm;
        font-size: 7pt;
        letter-spacing: 1px;
        background: rgba(0, 0, 0, .25);
        border-top: 1px solid rgba(255, 255, 255, .15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .75);
    }

    .id-card-valid {
        font-size: 6pt;
        text-align: right;
    }

    @media print {
        @page {
            size: 90mm 145mm;
            margin: 5mm;
        }

        .id-card {
            box-shadow: none;
            border-radius: 0;
            width: 100%;
            min-height: 100%;
        }

        .navbar, footer, .btn, .no-print {
            display: none !important;
        }
    }
</style>
@endsection