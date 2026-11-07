@extends('layouts.app')

@section('title', 'Pendaftaran Konfirmasi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body p-4 text-center">
                    <div class="display-1 text-primary mb-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h1 class="h3 mb-2">Pendaftaran Berhasil</h1>
                    <p class="text-muted">Silakan lanjutkan pembayaran kamu.</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="{{ route('public.participants.payment', $token) }}" class="btn btn-success btn-lg">
                            <i class="bi bi-credit-card me-1"></i>Bayar Sekarang
                        </a>
                        <a href="{{ route('public.participants.ticket', $token) }}" class="btn btn-outline-secondary btn-lg">
                            Lihat Tiket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection