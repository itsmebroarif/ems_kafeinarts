@extends('layouts.app')

@section('title', 'Buat Add-on')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header bg-body-secondary py-3">
                    <h4 class="mb-0">Buat Add-on: {{ $event->name }}</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.events.addons.store', $event->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Biaya Admin (Rp)</label>
                            <input type="number" name="fee" class="form-control" value="{{ old('fee') }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Stock</label>
                                <input type="number" name="qty" class="form-control" value="{{ old('qty') }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Terjual</label>
                                <input type="number" name="sold_qty" class="form-control" value="{{ old('sold_qty') }}" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_new" class="form-check-input" {{ old('is_new') ? 'checked' : '' }}>
                                Baru
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" name="is_show_in_list" class="form-check-input" {{ old('is_show_in_list') ? 'checked' : '' }}>
                                Tampil di daftar
                            </label>
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
