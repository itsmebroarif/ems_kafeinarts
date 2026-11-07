@extends('layouts.app')

@section('title', 'Detail Pendaftar')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow">
                <div class="card-header bg-body-secondary py-3">
                    <h4 class="mb-0">Detail Pendaftar</h4>
                </div>
                <div class="card-body p-4">
                    <h5 class="mb-4">Informasi Peserta</h5>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted">Nama</p>
                            <strong>{{ $registrant->full_name }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Email</p>
                            <strong>{{ $registrant->email }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">No. HP</p>
                            <strong>{{ $registrant->phone ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">NIK</p>
                            <strong>{{ $registrant->nik ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Jenis Kelamin</p>
                            <strong>{{ $registrant->gender ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Provinsi</p>
                            <strong>{{ $registrant->province ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Kota</p>
                            <strong>{{ $registrant->city ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Kecamatan</p>
                            <strong>{{ $registrant->district ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Kelurahan</p>
                            <strong>{{ $registrant->village ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Kode Pos</p>
                            <strong>{{ $registrant->postal_code ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Tier Tiket</p>
                            <strong>{{ $registrant->ticketTier->name }}</strong>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">Voucher</p>
                            <strong>{{ $registrant->voucher ? $registrant->voucher->code : '-' }}</strong>
                        </div>
                    </div>

                    <h5 class="mb-3">Ringkasan Pembayaran</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>Subtotal</td>
                                    <td>Rp {{ number_format($registrant->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Add-ons</td>
                                    <td>Rp {{ number_format($registrant->addons_total, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Diskon</td>
                                    <td>- Rp {{ number_format($registrant->discount, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Admin</td>
                                    <td>Rp {{ number_format($registrant->fee, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total</strong></td>
                                    <td><strong>Rp {{ number_format($registrant->total, 0, ',', '.') }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">Aksi Admin</h5>
                    <form method="POST" action="{{ route('admin.events.registrants.update', [$event->id, $registrant->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $registrant->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $registrant->status == 'paid' ? 'selected' : '' }}>Lunas</option>
                                <option value="confirmed" {{ $registrant->status == 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                                <option value="rejected" {{ $registrant->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Is Paid</label>
                            <select name="is_paid" class="form-select">
                                <option value="1" {{ $registrant->is_paid ? 'selected' : '' }}>Sudah</option>
                                <option value="0" {{ ! $registrant->is_paid ? 'selected' : '' }}>Belum</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transfer ID / Bukti Bayar</label>
                            <input type="text" name="exchange_id" class="form-control" value="{{ old('exchange_id', $registrant->exchange_id) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="notes" class="form-control">{{ old('notes', $registrant->notes) }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
