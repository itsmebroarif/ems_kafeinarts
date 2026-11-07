@extends('layouts.app')

@section('title', 'Papan Event')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">Papan Event</h1>
            <p class="text-muted mb-0 small">
                Seret kartu event untuk memindahkannya antar kolom.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul me-1"></i>Tampilan Tabel
            </a>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Buat Event
            </a>
        </div>
    </div>

    {{-- Toast notifikasi aksi drag & drop --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
        <div id="boardToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="boardToastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Tutup"></button>
            </div>
        </div>
    </div>

    <div class="board-scroll pt-3">
        <div class="board-lane d-flex gap-3 align-items-start">
            @foreach ($columns as $status => $column)
                <div class="board-column" data-status="{{ $status }}">
                    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                        <span class="fw-semibold small d-flex align-items-center gap-1">
                            <span class="badge bg-{{ $column['color'] }} rounded-pill">
                                <i class="bi {{ $column['icon'] }}"></i>
                            </span>
                            {{ $column['label'] }}
                        </span>
                        <span class="badge text-bg-light border rounded-pill" data-count-for="{{ $status }}">
                            {{ $grouped[$status]->count() }}
                        </span>
                    </div>

                    <div class="text-muted small px-1 mb-2" style="font-size: .72rem;">
                        {{ $column['hint'] }}
                    </div>

                    <div class="board-dropzone rounded-3 p-2" data-dropzone="{{ $status }}">
                        @forelse ($grouped[$status] as $event)
                            <article class="board-card card border-0 shadow-sm mb-2" draggable="true"
                                data-event-id="{{ $event->id }}" data-board-status="{{ $event->board_status }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <x-event-avatar :event="$event" :size="34" />
                                        <span class="badge rounded-pill bg-{{ $event->is_published ? 'success' : 'secondary' }} ms-auto"
                                            style="font-size: .65rem;">
                                            {{ $event->is_published ? 'Tayang' : 'Draf' }}
                                        </span>
                                    </div>

                                    <h6 class="fw-bold mb-1" style="font-size: .9rem;">
                                        <a href="{{ route('admin.events.edit', $event->id) }}"
                                            class="text-decoration-none">
                                            {{ $event->name }}
                                        </a>
                                    </h6>

                                    <div class="mb-2">
                                        <x-event-category-badge :event="$event" />
                                        <x-event-type-badge :event="$event" class="ms-1" />
                                    </div>

                                    <div class="small text-muted d-flex flex-column gap-1 mb-2">
                                        <span>
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $event->starts_at ? \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y H:i') : 'Belum diatur' }}
                                        </span>
                                        <span>
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $event->location ?? 'Online' }}
                                        </span>
                                        <span>
                                            <i class="bi bi-people me-1"></i>
                                            {{ $event->registrants->count() }} pendaftar
                                        </span>
                                    </div>

                                    {{-- Pemindah manual (alternatif drag & drop, berguna di layar sentuh) --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary w-100 dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-arrows-move me-1"></i>Pindahkan
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @foreach ($columns as $target => $targetColumn)
                                                @continue($target === $event->board_status)
                                                <li>
                                                    <button type="button" class="dropdown-item"
                                                        data-move-to="{{ $target }}"
                                                        data-event-name="{{ $event->name }}">
                                                        <i class="bi {{ $targetColumn['icon'] }} me-2"></i>
                                                        {{ $targetColumn['label'] }}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="board-empty text-center text-muted small rounded-3 py-4 px-2">
                                <i class="bi bi-inbox d-block fs-4 mb-1"></i>
                                Tidak ada event
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .board-scroll {
        overflow-x: auto;
        padding-bottom: .5rem;
    }

    .board-lane {
        min-width: min-content;
    }

    .board-column {
        flex: 0 0 288px;
        width: 288px;
    }

    .board-dropzone {
        min-height: 220px;
        background-color: var(--bs-tertiary-bg, rgba(127, 127, 127, .08));
        border: 1px dashed var(--bs-border-color);
        transition: background-color .15s ease, border-color .15s ease;
    }

    .board-dropzone.drag-over {
        background-color: rgba(var(--bs-primary-rgb), .12);
        border-color: var(--bs-primary);
    }

    .board-card {
        border-radius: .75rem;
        cursor: grab;
        transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
    }

    .board-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .75rem 1.5rem -.75rem rgba(15, 23, 42, .3) !important;
    }

    .board-card.dragging {
        opacity: .45;
        cursor: grabbing;
    }

    .board-empty {
        border: 1px dashed var(--bs-border-color);
        background-color: var(--bs-body-bg);
    }
</style>

<script>
    (function () {
        var moveUrlTemplate = @json(route('admin.events.board.move', '__EVENT__'));
        var token = @json(csrf_token());
        var toastElement = document.getElementById('boardToast');
        var toastBody = document.getElementById('boardToastBody');
        var draggedCard = null;

        function showToast(message, success) {
            toastBody.textContent = message;
            toastElement.classList.toggle('text-bg-success', !!success);
            toastElement.classList.toggle('text-bg-danger', !success);
            bootstrap.Toast.getOrCreateInstance(toastElement, { delay: 3500 }).show();
        }

        function refreshCounts() {
            document.querySelectorAll('[data-count-for]').forEach(function (badge) {
                var status = badge.getAttribute('data-count-for');
                var zone = document.querySelector('[data-dropzone="' + status + '"]');
                var count = zone ? zone.querySelectorAll('.board-card').length : 0;
                badge.textContent = count;

                var empty = zone ? zone.querySelector('.board-empty') : null;
                if (zone && count === 0 && !empty) {
                    zone.insertAdjacentHTML('beforeend',
                        '<div class="board-empty text-center text-muted small rounded-3 py-4 px-2">' +
                        '<i class="bi bi-inbox d-block fs-4 mb-1"></i>Tidak ada event</div>');
                } else if (zone && count > 0 && empty) {
                    empty.remove();
                }
            });
        }

        function moveCard(card, status, silent) {
            var zone = document.querySelector('[data-dropzone="' + status + '"]');
            if (!zone || !card) {
                return Promise.resolve(false);
            }

            var previousStatus = card.getAttribute('data-board-status');
            var previousZone = document.querySelector('[data-dropzone="' + previousStatus + '"]');

            card.setAttribute('data-board-status', status);
            zone.appendChild(card);
            refreshCounts();

            return fetch(moveUrlTemplate.replace('__EVENT__', card.getAttribute('data-event-id')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ board_status: status })
            }).then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Gagal memindahkan event.');
                    }

                    if (!silent) {
                        showToast(data.message, true);
                    }

                    return true;
                });
            }).catch(function (error) {
                // Kembalikan kartu ke kolom semula bila gagal.
                card.setAttribute('data-board-status', previousStatus);
                if (previousZone) {
                    previousZone.appendChild(card);
                }
                refreshCounts();
                showToast(error.message || 'Gagal memindahkan event.', false);
                return false;
            });
        }

        document.addEventListener('dragstart', function (event) {
            var card = event.target.closest('.board-card');
            if (!card) {
                return;
            }

            draggedCard = card;
            card.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', card.getAttribute('data-event-id'));
        });

        document.addEventListener('dragend', function (event) {
            var card = event.target.closest('.board-card');
            if (card) {
                card.classList.remove('dragging');
            }

            document.querySelectorAll('.board-dropzone.drag-over').forEach(function (zone) {
                zone.classList.remove('drag-over');
            });

            draggedCard = null;
        });

        document.querySelectorAll('.board-dropzone').forEach(function (zone) {
            zone.addEventListener('dragover', function (event) {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                zone.classList.add('drag-over');
            });

            zone.addEventListener('dragleave', function (event) {
                if (!zone.contains(event.relatedTarget)) {
                    zone.classList.remove('drag-over');
                }
            });

            zone.addEventListener('drop', function (event) {
                event.preventDefault();
                zone.classList.remove('drag-over');

                var card = draggedCard || document.querySelector(
                    '.board-card[data-event-id="' + event.dataTransfer.getData('text/plain') + '"]');

                if (!card) {
                    return;
                }

                var targetStatus = zone.getAttribute('data-dropzone');
                if (card.getAttribute('data-board-status') === targetStatus) {
                    return;
                }

                moveCard(card, targetStatus, false);
            });
        });

        // Alternatif drag & drop: menu "Pindahkan" pada tiap kartu.
        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-move-to]');
            if (!button) {
                return;
            }

            var card = button.closest('.board-card');
            moveCard(card, button.getAttribute('data-move-to'), false);
        });
    })();
</script>
@endsection
