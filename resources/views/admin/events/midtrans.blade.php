@extends('layouts.app')

@section('title', 'Midtrans — ' . $event->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.events.edit', $event->id) }}" class="small text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Edit Event
            </a>
            <h1 class="h4 fw-bold mb-0 mt-1">Midtrans — {{ $event->name }}</h1>
            <p class="small text-muted mb-0">Koneksi & kesiapan pembayaran Midtrans Snap untuk event ini.</p>
        </div>
        <span class="badge {{ $isReady ? 'bg-success' : 'bg-warning text-dark' }} fs-6 px-3 py-2">
            @if ($isReady)
                <i class="bi bi-check-circle me-1"></i>Siap Dibayar Publik
            @else
                <i class="bi bi-exclamation-triangle me-1"></i>Belum Siap
            @endif
        </span>
    </div>

    <div class="alert alert-info small">
        <i class="bi bi-info-circle me-1"></i>
        Midtrans Snap <strong>membangun transaksi langsung</strong> dari data event (tier &amp; add-on) saat pembeli klik
        "Bayar Sekarang". Tidak perlu registrasi item/produk di Midtrans — setiap pembelian otomatis langsung terdaftar.
    </div>

    <div class="row g-4">
        {{-- Checklist kesiapan --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-body-secondary py-3">
                    <h4 class="h6 fw-bold mb-0"><i class="bi bi-clipboard-check me-1"></i>Checklist Kesiapan</h4>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach ($readiness as $item)
                            <li class="list-group-item d-flex gap-2 align-items-start">
                                <i class="bi {{ $item['ok'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} fs-5 mt-1"></i>
                                <div>
                                    <div class="fw-semibold {{ $item['ok'] ? '' : 'text-danger' }}">{{ $item['label'] }}</div>
                                    <div class="small text-muted">{{ $item['hint'] }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Preview item_details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-body-secondary py-3 d-flex justify-content-between align-items-center">
                    <h4 class="h6 fw-bold mb-0"><i class="bi bi-receipt me-1"></i>Item yang dikirim ke Midtrans</h4>
                    <span class="badge bg-secondary">{{ count($items) }} item(s)</span>
                </div>
                <div class="card-body p-0">
                    @if (empty($items))
                        <div class="p-4 text-center text-muted small">
                            Belum ada item berbayar — buat minimal satu tier dengan harga di atas 0.
                        </div>
                    @else
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="small text-muted">{{ $item['id'] }}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td class="text-end">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ $item['quantity'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Test koneksi --}}
    <div class="card border-0 shadow mt-4">
        <div class="card-header bg-body-secondary py-3 d-flex justify-content-between align-items-center">
            <h4 class="h6 fw-bold mb-0"><i class="bi bi-plug me-1"></i>Test Koneksi Midtrans</h4>
            @if ($isConfigured)
                <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Server Key Tersedia</span>
            @else
                <span class="badge bg-danger"><i class="bi bi-x-lg me-1"></i>Server Key Belum Diset</span>
            @endif
        </div>
        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success small">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('midtrans_test'))
                @php($test = session('midtrans_test'))
                <div class="card border bg-body-tertiary mb-3">
                    <div class="card-body p-3 small">
                        <div class="row g-2">
                            <div class="col-sm-4"><span class="text-muted">HTTP Status</span><br><strong>{{ $test['http_status'] }}</strong></div>
                            <div class="col-sm-4"><span class="text-muted">Order ID</span><br><code>{{ $test['order_id'] }}</code></div>
                            <div class="col-sm-4"><span class="text-muted">Status</span><br>
                                <span class="fw-bold {{ $test['token'] ? 'text-success' : 'text-danger' }}">{{ $test['token'] ? 'OK (transaksi dibuat)' : 'Gagal' }}</span></div>
                        </div>
                        @if ($test['redirect_url'])
                            <div class="mt-2">
                                <span class="text-muted">Redirect URL:</span><br>
                                <a href="{{ $test['redirect_url'] }}" target="_blank" rel="noopener" class="small text-break">{{ $test['redirect_url'] }}</a>
                            </div>
                        @endif
                        @if ($test['message'])
                            <div class="mt-2 text-danger"><i class="bi bi-exclamation-triangle me-1"></i>{{ $test['message'] }}</div>
                        @endif
                    </div>
                </div>
            @endif

            <p class="text-muted small">
                Tombol ini memanggil langsung ke Midtrans Snap API dengan transaksi probe Rp 1.000 (sandbox).
                Berguna untuk memastikan kredensial &amp; koneksi jaringan kamu benar, tanpa perlu membuat transaksi sungguhan.
            </p>

            <form method="POST" action="{{ route('admin.events.midtrans.test', $event->id) }}" class="d-flex flex-wrap align-items-end gap-2">
                @csrf
                <div>
                    <label class="form-label small mb-1">Nominal Probe (Rp)</label>
                    <input type="number" name="amount" class="form-control" value="1000" min="1" style="width: 160px;">
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plug me-1"></i>Test Koneksi Midtrans
                </button>
                <a href="{{ route('public.events.show', $event->slug) }}" target="_blank" rel="noopener"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka Halaman Publik
                </a>
            </form>
        </div>
    </div>
</div>
@endsection