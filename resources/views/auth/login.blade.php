@extends('layouts.app')

@section('title', 'Masuk Admin')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-ticket-perforated-fill text-warning fs-1 mb-2"></i>
                    <h1 class="h4 fw-bold">Masuk</h1>
                    <p class="text-muted small">Masuk ke dashboard admin Kafeinarts EMS</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                placeholder="admin@kafeinarts.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                                required>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="remember">Ingat saya</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                    </button>
                </form>

                <div class="text-center mt-3">
                    <span class="small text-muted">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="small fw-semibold text-decoration-none">Daftar</a>
                </div>
            </div>

            <p class="text-center small text-muted mt-3 mb-0">
                Default: <strong>admin@kafeinarts.com</strong> / <strong>password</strong>
            </p>
        </div>
    </div>
</div>
@endsection