@extends('layouts.app')

@section('title', 'Pembayaran Selesai')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-body p-4 text-center">
                    <div class="display-1 text-success mb-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h1 class="h3 mb-2">Pembayaran Berhasil</h1>
                    <p class="text-muted">Pembayaran telah berhasil diproses dan tiket akan dikirim melalui email.</p>

                    <div class="mt-4">
                        <a href="{{ route('public.participants.ticket', $registrant->participant_token) }}" class="btn btn-primary">Lihat Tiket</a>
                        <a href="{{ route('public.events.index') }}" class="btn btn-outline-secondary">Kembali ke Event</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
