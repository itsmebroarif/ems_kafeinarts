@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-body-secondary py-3 d-flex align-items-center gap-3">
                    <div class="position-relative flex-shrink-0">
                        <div class="rounded-circle overflow-hidden border border-3 border-white shadow-sm d-flex align-items-center justify-content-center bg-secondary"
                            style="width: 72px; height: 72px;">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Foto {{ $user->name }}"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span class="fs-4 fw-bold text-white">{{ $user->initials() }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $user->name }}</h3>
                        <small class="text-muted">Kelola informasi profil Anda</small>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success py-2 small">
                            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h4 class="h6 fw-bold text-uppercase text-muted mb-3">Foto &amp; Identitas</h4>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Foto Profil</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                                <div class="form-text">JPG/PNG/WEBP, maks 2 MB. Kosongkan untuk memakai avatar inisial.</div>
                                @if ($user->avatar)
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="remove_avatar" value="1" class="form-check-input" id="removeAvatar">
                                        <label class="form-check-label small" for="removeAvatar">Hapus foto saat ini</label>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"
                                    placeholder="contoh: 081234567890">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alamat</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                            </div>
                        </div>

                        <h4 class="h6 fw-bold text-uppercase text-muted mb-3">Sosial Media</h4>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-instagram text-danger me-1"></i>Instagram</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="instagram" class="form-control"
                                        value="{{ old('instagram', $user->instagram) }}" placeholder="username atau URL">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-twitter-x me-1"></i>X / Twitter</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="twitter" class="form-control"
                                        value="{{ old('twitter', $user->twitter) }}" placeholder="username atau URL">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-linkedin me-1 text-primary"></i>LinkedIn</label>
                                <input type="text" name="linkedin" class="form-control"
                                    value="{{ old('linkedin', $user->linkedin) }}" placeholder="URL profil atau username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="bi bi-github me-1"></i>GitHub</label>
                                <input type="text" name="github" class="form-control"
                                    value="{{ old('github', $user->github) }}" placeholder="username atau URL">
                            </div>
                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-globe me-1 text-success"></i>Website / Portofolio</label>
                                <input type="text" name="website" class="form-control"
                                    value="{{ old('website', $user->website) }}" placeholder="https://contoh.com">
                                <div class="form-text">Boleh isi URL atau langsung domain — akan dinormalisasi otomatis.</div>
                            </div>
                        </div>

                        @if ($user->socialLinks() !== [])
                            <div class="mb-4">
                                <div class="small fw-bold text-muted mb-2">Link Aktif:</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($user->socialLinks() as $link)
                                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi {{ $link['icon'] }} me-1"></i>{{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow mt-4">
                <div class="card-header bg-body-secondary py-3">
                    <h4 class="h6 fw-bold mb-0"><i class="bi bi-shield-lock me-1"></i>Ganti Password</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" minlength="8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-key me-1"></i>Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection