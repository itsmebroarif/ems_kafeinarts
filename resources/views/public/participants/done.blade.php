@extends('layouts.app')

@section('title', 'Pembayaran Selesai')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body p-4 text-center">
                    @if ($registrant->is_paid)
                        <div class="display-1 text-success mb-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h1 class="h3 mb-2">Pembayaran Berhasil</h1>
                        <p class="text-muted">Pembayaran telah berhasil diproses. Tiket kamu tersedia di halaman tiket.</p>

                        <div class="mt-4">
                            <a href="{{ route('public.participants.ticket', $registrant->participant_token) }}" class="btn btn-primary">Lihat Tiket</a>
                            <a href="{{ route('public.events.index') }}" class="btn btn-outline-secondary">Kembali ke Event</a>
                        </div>
                    @elseif ($registrant->midtrans_status === 'pending')
                        <div class="display-1 text-warning mb-3">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <h1 class="h3 mb-2">Menunggu Pembayaran</h1>
                        <p class="text-muted">Pembayaran belum dikonfirmasi oleh Midtrans. Selesaikan pembayaran kamu untuk mendapatkan tiket.</p>

                        <div class="mt-4">
                            <a href="{{ route('public.participants.payment', $registrant->participant_token) }}" class="btn btn-warning">Lanjutkan Pembayaran</a>
                            <a href="{{ route('public.events.index') }}" class="btn btn-outline-secondary">Kembali ke Event</a>
                        </div>
                    @else
                        <div class="display-1 text-danger mb-3">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <h1 class="h3 mb-2">Pembayaran Dibatalkan</h1>
                        <p class="text-muted">Transaksi dibatalkan/expired. Kamu bisa mencoba kembali dari halaman pembayaran.</p>

                        <div class="mt-4">
                            <a href="{{ route('public.participants.payment', $registrant->participant_token) }}" class="btn btn-outline-primary">Coba Lagi</a>
                            <a href="{{ route('public.events.index') }}" class="btn btn-outline-secondary">Kembali ke Event</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection