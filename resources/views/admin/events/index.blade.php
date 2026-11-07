@extends('layouts.app')

@section('title', 'Event')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">Semua Event</h1>
            <p class="text-muted mb-0 small">Kelola event, tier, add-on, voucher, dan pendaftar.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.board') }}" class="btn btn-outline-primary">
                <i class="bi bi-kanban me-1"></i>Papan Kanban
            </a>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Buat Event
            </a>
        </div>
    </div>

    <form method="get" class="mb-3">
        <div class="input-group" style="max-width: 420px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Cari nama atau lokasi..."
                value="{{ request('search') }}">
            <button class="btn btn-outline-secondary">Cari</button>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Event</th>
                        <th>Kategori</th>
                        <th>Papan</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Pendaftar</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <x-event-avatar :event="$event" :size="36" />
                                    <div>
                                        <a href="{{ route('admin.events.edit', $event->id) }}"
                                            class="fw-semibold text-decoration-none">
                                            {{ $event->name }}
                                        </a>
                                        <div class="small text-muted">{{ $event->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <x-event-category-badge :event="$event" />
                                <div class="mt-1">
                                    <x-event-type-badge :event="$event" />
                                </div>
                            </td>
                            <td>
                                @php
                                    $boardColumn = \App\Enums\EventBoardStatus::columns()[$event->board_status]
                                        ?? \App\Enums\EventBoardStatus::columns()[\App\Enums\EventBoardStatus::REGISTERED];
                                @endphp
                                <span class="badge rounded-pill bg-{{ $boardColumn['color'] }}">
                                    <i class="bi {{ $boardColumn['icon'] }} me-1"></i>{{ $boardColumn['label'] }}
                                </span>
                            </td>
                            <td>{{ $event->location ?? '-' }}</td>
                            <td class="small">
                                {{ $event->starts_at ? \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y') : '-' }}
                            </td>
                            <td>
                                <span class="badge text-bg-light border">
                                    <i class="bi bi-people me-1"></i>{{ $event->registrants_count }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $event->is_published ? 'success' : 'secondary' }}">
                                    {{ $event->is_published ? 'Tersedia' : 'Draf' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end flex-wrap">
                                    <a href="{{ route('admin.events.tiers.index', $event->id) }}"
                                        class="btn btn-sm btn-outline-primary">Tier</a>
                                    <a href="{{ route('admin.events.addons.index', $event->id) }}"
                                        class="btn btn-sm btn-outline-primary">Add-on</a>
                                    <a href="{{ route('admin.events.vouchers.index', $event->id) }}"
                                        class="btn btn-sm btn-outline-primary">Voucher</a>
                                    <a href="{{ route('admin.events.registrants.index', $event->id) }}"
                                        class="btn btn-sm btn-outline-info">Pendaftar</a>
                                    <a href="{{ route('admin.events.edit', $event->id) }}"
                                        class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus event ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Belum ada event.
                                <a href="{{ route('admin.events.create') }}">Buat event pertama</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($events->hasPages())
        <div class="mt-3">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
