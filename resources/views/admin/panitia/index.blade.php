@extends('layouts.app')

@section('title', 'Panitia — ' . $event->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.events.tiers.index', $event->id) }}" class="small text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Event
            </a>
            <h1 class="h4 fw-bold mb-0 mt-1">Panitia — {{ $event->name }}</h1>
            <p class="small text-muted mb-0">Kelola anggota kepanitiaan, role, dan payroll event ini.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <x-import-export-tools type="panitia" :event="$event" />
            <a href="{{ route('admin.events.registrants.index', $event->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-people me-1"></i>Pendaftar
            </a>
        </div>
    </div>

    {{-- Ringkasan Payroll --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Total Gaji Kotor</div>
                    <div class="h5 fw-bold mb-0">Rp {{ number_format($totalBase, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Total Bersih (Semua)</div>
                    <div class="h5 fw-bold mb-0">Rp {{ number_format($totalNet, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="small text-muted">Sudah Dibayar</div>
                    <div class="h5 fw-bold text-success mb-0">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Form tambah anggota --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow">
                <div class="card-header bg-body-secondary py-3">
                    <h4 class="h6 fw-bold mb-0"><i class="bi bi-person-plus me-1"></i>Tambah Anggota</h4>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.events.panitia.store', $event->id) }}" id="addMemberForm">
                        @csrf

                        <label class="form-label fw-semibold small">Tambah kepanitiaan dari:</label>
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="mode" id="modeExisting" value="existing" checked>
                            <label class="btn btn-outline-primary" for="modeExisting"><i class="bi bi-person-check me-1"></i>Akun Ada</label>
                            <input type="radio" class="btn-check" name="mode" id="modeNew" value="new">
                            <label class="btn btn-outline-primary" for="modeNew"><i class="bi bi-person-plus me-1"></i>Akun Baru</label>
                        </div>

                        {{-- Akun lama --}}
                        <div class="mode-existing mb-3">
                            <label class="form-label">Pilih Akun</label>
                            <select name="user_id" class="form-select">
                                <option value="">— Pilih pengguna —</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Akun baru --}}
                        <div class="mode-new d-none">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama panitia">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="panitia@email.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" placeholder="0812...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Awal</label>
                                <input type="text" name="password" class="form-control" placeholder="min. 8 karakter">
                                <div class="form-text">Berikan password ini ke panitia.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role di Kepanitiaan</label>
                            <select name="role" class="form-select" required>
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bergabung Sejak</label>
                            <input type="date" name="joined_at" class="form-control" value="{{ now()->toDateString() }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i>Tambah ke Panitia
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar anggota + payroll --}}
        <div class="col-lg-8">
            @if ($members->isEmpty())
                <div class="card border-0 shadow">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-person-badge fs-1 text-secondary d-block mb-3"></i>
                        <h5 class="fw-bold">Belum ada panitia</h5>
                        <p class="text-muted small">Tambahkan anggota kepanitiaan menggunakan form di sebelah kiri.</p>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($members as $member)
                        @php
                            $payroll = $member->payrolls->sortByDesc('period_end')->first();
                            $initials = $member->user->initials();
                        @endphp
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="d-flex gap-3">
                                        {{-- Avatar --}}
                                        <div class="rounded-circle overflow-hidden border bg-secondary flex-shrink-0 d-flex align-items-center justify-content-center"
                                            style="width: 64px; height: 64px;">
                                            @if ($member->user->avatar_url)
                                                <img src="{{ $member->user->avatar_url }}" alt="{{ $member->user->name }}"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <span class="fw-bold text-white">{{ $initials }}</span>
                                            @endif
                                        </div>

                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between gap-2 flex-wrap">
                                                <div>
                                                    <div class="fw-bold text-truncate">{{ $member->user->name }}</div>
                                                    <div class="small text-muted text-truncate">{{ $member->user->email }}</div>
                                                    <div class="small text-muted">
                                                        <i class="bi bi-telephone me-1"></i>{{ $member->user->phone ?? '-' }}
                                                        <span class="mx-1">•</span>
                                                        <i class="bi bi-calendar-check me-1"></i>{{ optional($member->joined_at)->format('d M Y') ?? '-' }}
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <form method="POST" action="{{ route('admin.events.panitia.role', [$event->id, $member->id]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <select name="role" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()">
                                                            @foreach ($roles as $value => $label)
                                                                <option value="{{ $value }}" {{ $member->role === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.events.panitia.destroy', [$event->id, $member->id]) }}"
                                                        onsubmit="return confirm('Hapus {{ $member->user->name }} dari panitia?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus dari panitia">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('admin.events.panitia.id-card', [$event->id, $member->id]) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Cetak ID Card">
                                                        <i class="bi bi-person-vcard"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            {{-- Payroll form per anggota --}}
                                            <form method="POST" action="{{ route('admin.events.panitia.payroll.store', [$event->id, $member->id]) }}"
                                                class="payroll-form row g-2 align-items-end mt-2 border-top pt-2">
                                                @csrf
                                                <div class="col-6 col-md-3">
                                                    <label class="form-label small mb-0">Gaji Pokok</label>
                                                    <input type="number" class="form-control form-control-sm payroll-base" name="base_salary" min="0" step="1000"
                                                        value="{{ old('base_salary', $payroll?->base_salary ?? 0) }}" required>
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="form-label small mb-0">Tunjangan</label>
                                                    <input type="number" class="form-control form-control-sm payroll-allowance" name="allowance" min="0" step="1000"
                                                        value="{{ old('allowance', $payroll?->allowance ?? 0) }}">
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="form-label small mb-0">Lembur</label>
                                                    <input type="number" class="form-control form-control-sm payroll-overtime" name="overtime" min="0" step="1000"
                                                        value="{{ old('overtime', $payroll?->overtime ?? 0) }}">
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="form-label small mb-0">Potongan</label>
                                                    <input type="number" class="form-control form-control-sm payroll-deduction" name="deduction" min="0" step="1000"
                                                        value="{{ old('deduction', $payroll?->deduction ?? 0) }}">
                                                </div>
                                                <div class="col-12 col-md-3 d-flex flex-column align-items-md-end">
                                                    <div class="small text-muted">Bersih</div>
                                                    <div class="fw-bold text-primary payroll-net">Rp {{ number_format((float) ($payroll?->net_salary ?? 0), 0, ',', '.') }}</div>
                                                </div>
                                                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if ($payroll)
                                                            <span class="badge {{ $payroll->status === 'paid' ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $payroll->status === 'paid' ? 'LUNAS' : 'DRAFT' }}
                                                            </span>
                                                            @if ($payroll->period_start)
                                                                <span class="small text-muted">{{ $payroll->period_start->format('d M Y') }} — {{ $payroll->period_end?->format('d M Y') }}</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-light text-dark">BELUM ADA PAYROLL</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                                                        @if ($payroll)
                                                            <form method="POST" action="{{ route('admin.events.panitia.payroll.pay', [$event->id, $member->id, $payroll->id]) }}">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm {{ $payroll->status === 'paid' ? 'btn-outline-secondary' : 'btn-success' }}">
                                                                    @if ($payroll->status === 'paid')
                                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Batalkan
                                                                    @else
                                                                        <i class="bi bi-cash me-1"></i>Tandai Lunas
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Toggle form tambah akun lama vs akun baru
    (function () {
        var existing = document.getElementById('modeExisting');
        var newMode = document.getElementById('modeNew');
        var existingBoxes = document.querySelectorAll('.mode-existing');
        var newBoxes = document.querySelectorAll('.mode-new');

        function toggle() {
            var isExisting = existing.checked;
            existingBoxes.forEach(function (el) { el.classList.toggle('d-none', !isExisting); });
            newBoxes.forEach(function (el) { el.classList.toggle('d-none', isExisting); });

            // Atur required
            newBoxes.forEach(function (el) {
                el.querySelectorAll('input, select').forEach(function (input) { input.required = !isExisting; });
            });
            existingBoxes.forEach(function (el) {
                el.querySelectorAll('select').forEach(function (select) { select.required = isExisting; });
            });
        }

        [existing, newMode].forEach(function (radio) {
            radio.addEventListener('change', toggle);
        });
        toggle();
    })();

    // Hitung gaji bersih secara live
    (function () {
        document.querySelectorAll('.payroll-form').forEach(function (form) {
            var net = form.querySelector('.payroll-net');
            var update = function () {
                var base = parseFloat(form.querySelector('.payroll-base').value) || 0;
                var allow = parseFloat(form.querySelector('.payroll-allowance').value) || 0;
                var ot = parseFloat(form.querySelector('.payroll-overtime').value) || 0;
                var ded = parseFloat(form.querySelector('.payroll-deduction').value) || 0;
                var total = Math.round(base + allow + ot - ded);
                net.textContent = 'Rp ' + total.toLocaleString('id-ID');
            };
            form.querySelectorAll('.payroll-base, .payroll-allowance, .payroll-overtime, .payroll-deduction')
                .forEach(function (input) { input.addEventListener('input', update); });
        });
    })();
</script>
@endsection