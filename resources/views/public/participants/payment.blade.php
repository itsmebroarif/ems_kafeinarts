@extends('layouts.app')

@section('title', 'Proses Pembayaran')

@push('scripts')
    <script src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="badge text-bg-warning rounded-pill px-3 py-2 mb-2">
                            <i class="bi bi-credit-card me-1"></i>Midtrans Sandbox
                        </span>
                        <h1 class="h4">Proses Pembayaran</h1>
                    </div>

                    <div class="card bg-body-secondary border-0 mb-4">
                        <div class="card-body">
                            <h5 class="card-title">{{ $registrant->ticketTier->name }}</h5>
                            <p class="mb-1"><strong>{{ $registrant->event->name }}</strong></p>
                            <p class="mb-0 small text-muted">
                                Atas nama: {{ $registrant->full_name }}<br>
                                Email: {{ $registrant->email }}<br>
                                ID Peserta: {{ $registrant->participant_token }}
                            </p>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Subtotal tiket</span>
                                <span>Rp {{ number_format($simulation['subtotal'], 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Add-ons</span>
                                <span>Rp {{ number_format($simulation['addons'], 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Diskon</span>
                                <span>-Rp {{ number_format($simulation['discount'], 0, ',', '.') }}</span>
                            </div>
                            @if ($simulation['fee'] > 0)
                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                    <span>Fee layanan</span>
                                    <span>Rp {{ number_format($simulation['fee'], 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total Bayar</span>
                                <span id="totalAmount">Rp {{ number_format($simulation['total'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        Pembayaran diproses aman lewat <strong>Midtrans Sandbox</strong>.
                        Setelah bayar, status akan diverifikasi otomatis.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success btn-lg" id="payNowBtn">
                            <i class="bi bi-shield-check me-1"></i>Bayar Sekarang dengan Midtrans
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="payRedirectBtn">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Atau bayar di halaman Midtrans
                        </button>
                    </div>

                    @if ($registrant->is_paid)
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="bi bi-check-circle me-1"></i>Pesanan ini sudah terbayar.
                            <a href="{{ route('public.participants.done', $token) }}" class="alert-link">Lihat bukti</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var payBtn = document.getElementById('payNowBtn');
        var redirectBtn = document.getElementById('payRedirectBtn');
        var token = @json($token);
        var routes = {
            checkout: @json(route('public.participants.checkout', $token)),
            sync: @json(route('public.participants.midtrans.sync', $token)),
            done: @json(route('public.participants.done', $token)),
        };
        var csrf = '{{ csrf_token() }}';

        function resetButtons() {
            payBtn.disabled = false;
            redirectBtn.disabled = false;
            payBtn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Bayar Sekarang dengan Midtrans';
        }

        function checkout() {
            payBtn.disabled = true;
            redirectBtn.disabled = true;
            payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyiapkan pembayaran...';

            return fetch(routes.checkout, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            }).then(function (response) {
                return response.json().then(function (body) {
                    if (!response.ok) {
                        throw new Error(body.error || body.message || 'Gagal membuat transaksi Midtrans.');
                    }
                    return body;
                });
            });
        }

        function onSuccess() {
            fetch(routes.sync, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            }).then(function () {
                window.location.href = routes.done;
            }).catch(function () {
                window.location.href = routes.done;
            });
        }

        payBtn.addEventListener('click', function () {
            checkout().then(function (data) {
                if (typeof window.snap === 'undefined') {
                    throw new Error('Snap JS gagal dimuat.');
                }

                window.snap.pay(data.token, {
                    onSuccess: function () { onSuccess(); },
                    onPending: function () { window.location.href = routes.done; },
                    onError: function (result) {
                        alert('Pembayaran gagal: ' + JSON.stringify(result));
                        resetButtons();
                    },
                    onClose: function () { resetButtons(); },
                });

                resetButtons();
            }).catch(function (error) {
                alert(error.message);
                resetButtons();
            });
        });

        redirectBtn.addEventListener('click', function () {
            checkout().then(function (data) {
                window.location.href = data.redirect_url;
            }).catch(function (error) {
                alert(error.message);
                resetButtons();
            });
        });
    })();
</script>
@endpush
@endsection