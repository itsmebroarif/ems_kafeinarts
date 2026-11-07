@extends('layouts.app')

@section('title', 'Pendaftar')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Pendaftar: {{ $event->name }}</h1>
        <x-import-export-tools :event="$event" type="registrants" />
    </div>

    <form method="get" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary">Cari</button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="is_paid" class="form-select">
                    <option value="">Semua Bayar</option>
                    <option value="1" {{ request('is_paid') == 1 ? 'selected' : '' }}>Sudah</option>
                    <option value="0" {{ request('is_paid') == 0 ? 'selected' : '' }}>Belum</option>
                </select>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Tier</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registrants as $registrant)
                    <tr>
                        <td>{{ $registrant->full_name }}</td>
                        <td>{{ $registrant->email }}</td>
                        <td>{{ $registrant->ticketTier->name }}</td>
                        <td>Rp {{ number_format($registrant->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $registrant->status === 'paid' ? 'success' : ($registrant->status === 'confirmed' ? 'primary' : 'warning') }}">{{ $registrant->status }}</span>
                        </td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.events.registrants.show', [$event->id, $registrant->id]) }}" class="btn btn-sm btn-outline-info">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Belum ada pendaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($registrants->hasPages())
        <div class="mt-3">
            {{ $registrants->links() }}
        </div>
    @endif
</div>
@endsection
