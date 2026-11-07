@extends('layouts.app')

@section('title', 'Tambah Result Event')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow">
                <div class="card-header pb-0">
                    <h3 class="fw-bold mb-0">Tambah Result Event</h3>
                </div>
                <div class="card-body p-4">
                    @if ($event)
                        <div class="mb-3">
                            <strong>Event:</strong> {{ $event->name }}
                            @if ($event->category)
                                <span class="text-muted small ms-2">Kategori: {{ $event->category->name }}</span>
                            @endif
                            @if ($event->board_status === 'result')
                                <span class="text-success small ms-2">Status: Result</span>
                            @else
                                <span class="text-warning small ms-2">Status: {{ ucfirst($event->board_status) }}</span>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.events.result.store', $event?.id ?? '') }}">
                        @csrf

                        @if ($event)
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pilih Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ ($event?.category_id ?? null) == $category->id ? 'selected' : '' }}
                                            {{ $category->is_sport ? 'data-sport="true"' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    Kategori dengan <strong>sport=true</strong> boleh dimasukkan ke kolom Result.
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Board Status</label>
                                <select name="board_status" class="form-select" required>
                                    <option value="registered" {{ ($event?.board_status ?? 'registered') == 'registered' ? 'selected' : '' }}>Event Terdaftar</option>
                                    <option value="starting" {{ ($event?.board_status ?? 'registered') == 'starting' ? 'selected' : '' }}>Event Akan Dimulai</option>
                                    <option value="closed" {{ ($event?.board_status ?? 'registered') == 'closed' ? 'selected' : '' }}>Event Ditutup</option>
                                    <option value="result" {{ ($event?.board_status ?? 'registered') == 'result' ? 'selected' : '' }}>Result</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Detail Result</label>
                                <small class="text-muted">
                                    Isi detail hasil event (skor, pemenang, dll). Untuk event sport/esport.
                                </small>
                                <textarea name="result_details" class="form-control" rows="3"
                                    placeholder="Contoh: Final: Tim A 2 - 1 Tim B, Pemenang: Tim A"></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Skor Akhir</label>
                                <input type="text" name="final_score" class="form-control"
                                    placeholder="Contoh: 2-1 atau 3-0">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pemenang</label>
                                <input type="text" name="winner" class="form-control"
                                    placeholder="Contoh: Tim A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">MVP / Pemain Terbaik</label>
                                <input type="text" name="mvp" class="form-control"
                                    placeholder="Contoh: John Doe">
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Result
                            </button>
                            @if ($event)
                                <a href="{{ route('admin.events.board') }}"
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Papan
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection