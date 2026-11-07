@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <h1 class="h4 mb-4 text-center">Ringkasan Pembayaran</h1>

                    <div class="card card-primary mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Tier Tiket</h5>
                            <p class="card-text"><strong>{{ $tier->name }}</strong></p>
                            <p class="card-text"><strong>TOTAL:</strong> Rp {{ number_format($tier->price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $subtotal = 0; @endphp
                                @foreach ($payload['addons'] ?? [] as $addonId => $qty)
                                    @php
                                        $addon = collect($event->addons)->firstWhere('id', $addonId);
                                        $price = $addon ? $addon->price : 0;
                                        $subtotal += $price * $qty;
                                    @endphp
                                    <tr>
                                        <td>{{ $addon ? $addon->name : 'Unknown' }}</td>
                                        <td>{{ $qty }}</td>
                                        <td>Rp {{ number_format($price, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($price * $qty, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-info mt-4">
                        {{ __('Data pendaftaran harus dimasukkan ke dalam session sebelum proses pembayaran.') }}
                    </div>

                    <form method="POST" action="{{ route('public.events.store', $event->slug) }}">
                        @csrf
                        <input type="hidden" name="ticket_tier_id" value="{{ $tier->id }}">
                        @foreach ($payload['addons'] ?? [] as $addonId => $qty)
                            <input type="hidden" name="addons[{{ $addonId }}]" value="{{ $qty }}">
                        @endforeach
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Simpan & Lanjutkan Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
