@php
    // Nomor & tanggal surat dihitung sekali agar konsisten antara preview dan PDF.
    $issuedAt = $issuedAt ?? now()->format('d M Y H:i');
    $documentNumber = $documentNumber ?? 'SCE/'.strtoupper((string) $registrant->participant_token).'/'.now()->format('Y');
@endphp

<div class="checkout-sheet">
    <div class="kop">
        <table>
            <tr>
                <td style="width: 80px;">
                    <div class="kop-logo">K</div>
                </td>
                <td>
                    <div class="kop-org">{{ config('app.name') }}</div>
                    <div class="kop-sub">Event Management System</div>
                    <div class="kop-sub">{{ config('app.url') }}</div>
                </td>
                <td class="kop-meta">
                    No. {{ $documentNumber }}<br>
                    Tanggal: {{ $issuedAt }}<br>
                    Tempat: {{ $event->location ?? 'Indonesia' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title">Surat Checkout Event</div>
    <div class="doc-number">Bukti Pendaftaran &amp; Pembayaran Peserta</div>

    <p class="intro">
        Yang bertanda tangan di bawah ini menyatakan bahwa peserta dengan data:
    </p>

    <h2 class="section">Data Peserta</h2>
    <table class="data">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="value">{{ $registrant->full_name }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="value">{{ $registrant->email }}</td>
        </tr>
        <tr>
            <td class="label">No. HP</td>
            <td class="value">{{ $registrant->phone ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kota / Kecamatan</td>
            <td class="value">
                {{ $registrant->city ?? '-' }}{{ $registrant->district ? ' / '.$registrant->district : '' }}
            </td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="value">{{ $registrant->address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">ID Peserta</td>
            <td class="value">{{ $registrant->participant_token }}</td>
        </tr>
    </table>

    <h2 class="section">Data Event</h2>
    <table class="data">
        <tr>
            <td class="label">Nama Event</td>
            <td class="value">{{ $event->name }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Event</td>
            <td class="value">{{ $event->eventTypeLabel() }}</td>
        </tr>
        <tr>
            <td class="label">Kategori</td>
            <td class="value">{{ $event->category?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="value">
                {{ $event->starts_at ? \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') : '-' }}
                @if ($event->ends_at)
                    s/d {{ \Illuminate\Support\Carbon::parse($event->ends_at)->format('d M Y H:i') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Lokasi</td>
            <td class="value">
                {{ $event->location ?? '-' }}
                @if ($event->venue_details)
                    <br><span style="font-weight: normal; color: #555;">{{ $event->venue_details }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Tier Tiket</td>
            <td class="value">{{ $tier?->name ?? '-' }}</td>
        </tr>
    </table>

    <h2 class="section">Rincian Pembelian</h2>
    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th class="num">Qty</th>
                <th class="num">Harga</th>
                <th class="num">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tier Tiket — {{ $tier?->name ?? '-' }}</td>
                <td class="num">1</td>
                <td class="num">Rp {{ number_format((float) ($tier->price ?? 0), 0, ',', '.') }}</td>
                <td class="num">Rp {{ number_format((float) ($tier->price ?? 0), 0, ',', '.') }}</td>
            </tr>
            @foreach ($registrant->addons as $addon)
                <tr>
                    <td>{{ $addon->addon?->name ?? 'Add-on' }}</td>
                    <td class="num">{{ $addon->qty }}</td>
                    <td class="num">Rp {{ number_format((float) $addon->price, 0, ',', '.') }}</td>
                    <td class="num">Rp {{ number_format((float) $addon->price * (int) $addon->qty, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            @if ((float) $registrant->discount > 0)
                <tr>
                    <td>Voucher {{ $registrant->voucher?->code ? '('.$registrant->voucher->code.')' : '' }}</td>
                    <td class="num">1</td>
                    <td class="num">-</td>
                    <td class="num">-Rp {{ number_format((float) $registrant->discount, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <table class="total">
        <tr>
            <td>Subtotal</td>
            <td class="num">Rp {{ number_format((float) $registrant->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Add-on</td>
            <td class="num">Rp {{ number_format((float) $registrant->addons_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="num">-Rp {{ number_format((float) $registrant->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Fee Layanan</td>
            <td class="num">Rp {{ number_format((float) $registrant->fee, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand">
            <td>TOTAL</td>
            <td class="num">Rp {{ number_format((float) $registrant->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="status-box">
        <table class="data" style="margin-bottom: 0;">
            <tr>
                <td class="label">Status Pembayaran</td>
                <td class="value">
                    @if ($registrant->is_paid)
                        <span class="status-paid">LUNAS</span>
                    @else
                        <span class="status-unpaid">BELUM DIBAYAR</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Waktu Pembayaran</td>
                <td class="value">
                    {{ $registrant->paid_at ? \Illuminate\Support\Carbon::parse($registrant->paid_at)->format('d M Y H:i') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="label">Metode / Referensi</td>
                <td class="value">
                    {{ $registrant->paid_by ?? '-' }}{{ $registrant->exchange_id ? ' — '.$registrant->exchange_id : '' }}
                </td>
            </tr>
        </table>
    </div>

    <p class="closing">
        Demikian surat ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
    </p>

    <table class="signatures">
        <tr>
            <td>
                Peserta,
                <div class="sign-name">{{ $registrant->full_name }}</div>
            </td>
            <td>
                Penyelenggara Event,
                <div class="sign-name">{{ config('app.name') }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini dibuat secara otomatis oleh {{ config('app.name') }} pada {{ $issuedAt }}.
        ID Peserta: {{ $registrant->participant_token }}.
    </div>
</div>
