<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>E-Ticket {{ $event->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 24px;
        }
        .ticket {
            border: 2px solid #1a1a1a;
            border-radius: 8px;
            padding: 24px;
            max-width: 800px;
            width: 100%;
        }
        .ticket-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
        }
        .ticket-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #2d6a4f;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
        }
        h1 { margin: 0; font-size: 20px; }
        .event-name { color: #555; font-size: 14px; margin-top: 4px; }
        .ticket-body {
            display: flex;
            gap: 24px;
        }
        .info-column { flex: 1; }
        .qr {
            border: 2px solid #1a1a1a;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            background: #f8faf9;
        }
        .qr img { max-width: 180px; max-height: 180px; }
        .qr-label { font-size: 11px; color: #555; margin-top: 6px; }
        .details { margin-top: 16px; }
        .detail-row {
            display: flex;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { width: 140px; font-weight: bold; color: #555; }
        .detail-value { flex: 1; }
        .ticket-footer {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #555;
            border-top: 1px solid #e9ecef;
            padding-top: 12px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #1a1a1a;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <div class="ticket-logo">K</div>
            <div>
                <h1>{{ $event->name }}</h1>
                <div class="event-name">{{ $event->location }} · {{ \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y') }}</div>
            </div>
        </div>

        <div class="ticket-body">
            <div class="info-column">
                <div class="qr">
                    @if ($registrant->ticketItems->isNotEmpty() && $registrant->ticketItems->first()->qr_code)
                        <img src="{{ $registrant->ticketItems->first()->qr_code }}" alt="QR Code">
                        <div class="qr-label">QR Code / Barcode</div>
                    @else
                        <div class="qr-label">QR Code belum tersedia</div>
                    @endif
                </div>
                <div class="details">
                    <div class="detail-row"><div class="detail-label">Peserta</div><div class="detail-value">{{ $registrant->full_name }}</div></div>
                    <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value">{{ $registrant->email }}</div></div>
                    <div class="detail-row"><div class="detail-label">No. HP</div><div class="detail-value">{{ $registrant->phone ?? '-' }}</div></div>
                    <div class="detail-row"><div class="detail-label">Event</div><div class="detail-value">{{ $event->name }}</div></div>
                    <div class="detail-row"><div class="detail-label">Lokasi</div><div class="detail-value">{{ $event->location }}</div></div>
                    <div class="detail-row"><div class="detail-label">Tanggal</div><div class="detail-value">{{ \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Tier</div><div class="detail-value">{{ $tier->name }}</div></div>
                    <div class="detail-row"><div class="detail-label">Invoice #</div><div class="detail-value">INV-{{ \Illuminate\Support\Str::upper((string) $registrant->participant_token) }}</div></div>
                </div>
            </div>
            <div class="info-column" style="border-left: 2px solid #1a1a1a; padding-left: 16px;">
                <div class="details" style="margin-top:0;">
                    <div class="detail-row"><div class="detail-label">Subtotal</div><div class="detail-value">Rp {{ number_format($registrant->subtotal, 0, ',', '.') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Addons</div><div class="detail-value">Rp {{ number_format($registrant->addons_total, 0, ',', '.') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Diskon</div><div class="detail-value">-Rp {{ number_format($registrant->discount, 0, ',', '.') }}</div></div>
                    <div class="detail-row"><div class="detail-label">Biaya</div><div class="detail-value">Rp {{ number_format($registrant->fee, 0, ',', '.') }}</div></div>
                    <div class="total-row">
                        <div>Total</div>
                        <div>Rp {{ number_format($registrant->total, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ticket-footer">
            <div>Event Organizer: {{ config('app.name') }}</div>
            <div>Kode: {{ $registrant->participant_token }}</div>
        </div>
    </div>
</body>
</html>
