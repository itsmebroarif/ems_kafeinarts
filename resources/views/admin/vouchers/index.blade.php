@extends('layouts.app')

@section('title', 'Voucher')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Voucher: {{ $event->name }}</h1>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <x-import-export-tools :event="$event" type="vouchers" />
            <a href="{{ route('admin.events.vouchers.create', $event->id) }}" class="btn btn-primary">+ Tambah Voucher</a>
        </div>
    </div>

    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari voucher..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Diskon</th>
                    <th>Total Pakai</th>
                    <th>Aktif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->code }}</td>
                        <td>{{ $voucher->is_percent ? $voucher->value . '%' : 'Rp ' . number_format($voucher->value, 0, ',', '.') }}</td>
                        <td>{{ $voucher->uses }} / {{ $voucher->total_uses }}</td>
                        <td>
                            <span class="badge bg-{{ $voucher->is_active ? 'success' : 'secondary' }}">{{ $voucher->is_active ? 'Aktif' : 'Non-aktif' }}</span>
                        </td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.events.vouchers.edit', [$event->id, $voucher->id]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.events.vouchers.destroy', [$event->id, $voucher->id]) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($vouchers->hasPages())
        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    @endif
</div>
@endsection
