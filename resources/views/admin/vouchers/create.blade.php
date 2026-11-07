@extends('layouts.app')

@section('title', 'Buat Voucher')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header bg-body-secondary py-3">
                    <h4 class="mb-0">Buat Voucher: {{ $event->name }}</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.events.vouchers.store', $event->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Diskon <span class="text-danger">*</span></label>
                            <select name="is_percent" class="form-select">
                                <option value="1" {{ old('is_percent') ? 'selected' : '' }}>Persentase</option>
                                <option value="0" {{ ! old('is_percent') ? 'selected' : '' }}>Nominal</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nilai Diskon (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="value" class="form-control" value="{{ old('value') }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_single_use" class="form-check-input" {{ old('is_single_use') ? 'checked' : '' }}>
                                Gunakan sekali
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" {{ old('is_active') ? 'checked' : '' }}>
                                Aktif
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Total Uses</label>
                                <input type="number" name="total_uses" class="form-control" value="{{ old('total_uses') }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Uses Hari Ini</label>
                                <input type="number" name="uses" class="form-control" value="{{ old('uses') }}" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_auto_apply" class="form-check-input" {{ old('is_auto_apply') ? 'checked' : '' }}>
                                Otomatis terapkan
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_show_in_list" class="form-check-input" {{ old('is_show_in_list') ? 'checked' : '' }}>
                                Tampil di daftar
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_show_on_front" class="form-check-input" {{ old('is_show_on_front') ? 'checked' : '' }}>
                                Tampil di halaman depan
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_show_on_payment" class="form-check-input" {{ old('is_show_on_payment') ? 'checked' : '' }}>
                                Tampil di halaman pembayaran
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expired At</label>
                            <input type="datetime-local" name="expired_at" class="form-control" value="{{ old('expired_at') }}">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
