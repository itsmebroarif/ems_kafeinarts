@extends('layouts.app')

@section('title', 'Edit Tier Tiket')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header bg-body-secondary py-3">
                    <h4 class="mb-0">Edit Tier: {{ $event->name }}</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.events.tiers.update', [$event->id, $tier->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $tier->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="{{ old('code', $tier->code) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $tier->slug) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $tier->price) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Biaya Admin (Rp)</label>
                            <input type="number" name="fee" class="form-control" value="{{ old('fee', $tier->fee) }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control">{{ old('description', $tier->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Stock</label>
                                <input type="number" name="stock_qty" class="form-control" value="{{ old('stock_qty', $tier->stock_qty) }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Terjual</label>
                                <input type="number" name="sold_qty" class="form-control" value="{{ old('sold_qty', $tier->sold_qty) }}" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_downloadable" class="form-check-input" {{ old('is_downloadable', $tier->is_downloadable) ? 'checked' : '' }}>
                                Dapat diunduh
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_show_in_list" class="form-check-input" {{ old('is_show_in_list', $tier->is_show_in_list) ? 'checked' : '' }}>
                                Tampil di daftar
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
