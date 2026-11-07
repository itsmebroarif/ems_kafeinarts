@extends('layouts.app')

@section('title', 'Tier Tiket')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Tier Tiket: {{ $event->name }}</h1>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <x-import-export-tools :event="$event" type="tiers" />
            <a href="{{ route('admin.events.tiers.create', $event->id) }}" class="btn btn-primary">+ Tambah Tier</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Harga</th>
                    <th>Stock</th>
                    <th>Terjual</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tiers as $tier)
                    <tr>
                        <td>{{ $tier->name }}</td>
                        <td>{{ $tier->code }}</td>
                        <td>Rp {{ number_format($tier->price, 0, ',', '.') }}</td>
                        <td>{{ $tier->stock_qty }}</td>
                        <td>{{ $tier->sold_qty }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.events.tiers.edit', [$event->id, $tier->id]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.events.tiers.destroy', [$event->id, $tier->id]) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Belum ada tier.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tiers->hasPages())
        <div class="mt-3">
            {{ $tiers->links() }}
        </div>
    @endif
</div>
@endsection
