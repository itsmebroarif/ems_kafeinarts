@extends('layouts.app')

@section('title', 'Proses Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <h1 class="h4 mb-4">Proses Pembayaran</h1>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">{{ $registrant->ticketTier->name }}</h5>
                            <p class="card-text">{{ $registrant->event->name }}</p>
                            <p class="card-text"><strong>Total:</strong> Rp {{ number_format($registrant->total, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('public.participants.payment.store', $token) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="virtual_acct">Virtual Account Bank</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="credit_card">Kartu Kredit</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Provider</label>
                            <input type="text" name="payment_provider" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Exchange ID / Virtual Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="exchange_id" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" value="{{ $simulation['total'] }}" required>
                        </div>

                        <input type="hidden" name="currency" value="{{ $simulation['currency'] ?? 'IDR' }}">
                        <input type="hidden" name="ip_address" value="{{ $request->ip() }}">
                        <input type="hidden" name="device_type" value="desktop">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Konfirmasi Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
