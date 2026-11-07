@extends('layouts.app')

@section('title', 'Daftar Admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-ticket-perforated-fill text-warning fs-1 mb-2"></i>
                    <h1 class="h4 fw-bold">Daftar</h1>
                    <p class="text-muted small">Buat akun admin baru</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                placeholder="Nama admin" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="nama@email.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control"
                                placeholder="Minimal 8 karakter" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Ulangi password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-person-plus me-1"></i>Daftar
                    </button>
                </form>

                <div class="text-center mt-3">
                    <span class="small text-muted">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="small fw-semibold text-decoration-none">Masuk</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection