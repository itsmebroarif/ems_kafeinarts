@extends('layouts.app')

@section('title', 'Add-on')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0">Add-on: {{ $event->name }}</h1>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <x-import-export-tools :event="$event" type="addons" />
            <a href="{{ route('admin.events.addons.create', $event->id) }}" class="btn btn-primary">+ Tambah Add-on</a>
        </div>
    </div>

    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari add-on..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stock</th>
                    <th>Terjual</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($addons as $addon)
                    <tr>
                        <td>{{ $addon->name }}</td>
                        <td>Rp {{ number_format($addon->price, 0, ',', '.') }}</td>
                        <td>{{ $addon->qty }}</td>
                        <td>{{ $addon->sold_qty }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.events.addons.edit', [$event->id, $addon->id]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form method="POST" action="{{ route('admin.events.addons.destroy', [$event->id, $addon->id]) }}" class="d-inline">
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

    @if ($addons->hasPages())
        <div class="mt-3">
            {{ $addons->links() }}
        </div>
    @endif
</div>
@endsection
