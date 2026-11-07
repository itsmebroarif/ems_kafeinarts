@extends('layouts.app')

@section('title', 'Lengkapi Pendaftaran')

@php
    $regSteps = [
        ['number' => 1, 'icon' => 'bi-person', 'label' => 'Data Diri'],
        ['number' => 2, 'icon' => 'bi-ticket-detailed', 'label' => 'Tiket & Add-on'],
        ['number' => 3, 'icon' => 'bi-clipboard-check', 'label' => 'Konfirmasi'],
    ];
@endphp

@section('content')
<div class="container py-4">
    <div class="row g-4 justify-content-center">
        {{-- Kolom kiri: wizard pendaftaran --}}
        <div class="col-lg-7 col-xl-8">
            <div class="card border-0 shadow overflow-hidden">
                {{-- Banner event di atas wizard --}}
                <x-event-banner :event="$event" :height="170" />

                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <x-event-avatar :event="$event" :size="52" />
                        <div class="min-w-0">
                            <h1 class="h5 fw-bold mb-1 text-truncate">{{ $event->name }}</h1>
                            <div class="d-flex flex-wrap gap-1">
                                <x-event-category-badge :event="$event" />
                                <x-event-type-badge :event="$event" />
                            </div>
                        </div>
                    </div>

                    {{-- Indikator langkah --}}
                    <ol class="reg-steps mb-4">
                        @foreach ($regSteps as $step)
                            <li class="reg-step-item {{ $step['number'] === 1 ? 'active' : '' }}"
                                data-step-indicator="{{ $step['number'] }}">
                                <span class="step-number">{{ $step['number'] }}</span>
                                <span class="step-label"><i class="bi {{ $step['icon'] }} me-1"></i>{{ $step['label'] }}</span>
                            </li>
                        @endforeach
                    </ol>

                    @error('registration')
                        <div class="alert alert-danger py-2 small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('public.events.store', $event->slug) }}"
                        id="registrationWizard">
                        @csrf

                        <input type="hidden" name="ticket_tier_id" value="{{ $tier->id }}">

                        {{-- Langkah 1: Data Diri --}}
                        <section class="reg-pane" data-step="1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ old('full_name') }}" required autofocus>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-6 mb-3 location-widget" data-level="provinsi">
                                    <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" name="province" class="form-control location-input"
                                        placeholder="Ketik untuk mencari provinsi..." required autocomplete="off"
                                        value="{{ old('province') }}">
                                    <div class="location-list list-group position-absolute w-100 shadow border rounded-3"
                                        role="listbox" hidden style="max-height: 240px; overflow-y: auto; z-index: 1080;">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 location-widget" data-level="kota">
                                    <label class="form-label">Kota / Kabupaten <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control location-input"
                                        placeholder="{{ old('province') ? 'Ketik untuk mencari kota / kabupaten...' : 'Pilih provinsi dulu...' }}"
                                        required autocomplete="off"
                                        value="{{ old('city') }}" @unless(old('province')) disabled @endunless>
                                    <div class="location-list list-group position-absolute w-100 shadow border rounded-3"
                                        role="listbox" hidden style="max-height: 240px; overflow-y: auto; z-index: 1080;">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 location-widget" data-level="kecamatan">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" name="district" class="form-control location-input"
                                        placeholder="{{ old('city') ? 'Ketik untuk mencari kecamatan...' : 'Pilih kota / kabupaten dulu...' }}"
                                        autocomplete="off"
                                        value="{{ old('district') }}" @unless(old('city')) disabled @endunless>
                                    <div class="location-list list-group position-absolute w-100 shadow border rounded-3"
                                        role="listbox" hidden style="max-height: 240px; overflow-y: auto; z-index: 1080;">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3 location-widget" data-level="kelurahan">
                                    <label class="form-label">Kelurahan</label>
                                    <input type="text" name="village" class="form-control location-input"
                                        placeholder="{{ old('district') ? 'Ketik untuk mencari kelurahan...' : 'Pilih kecamatan dulu...' }}"
                                        autocomplete="off"
                                        value="{{ old('village') }}" @unless(old('district')) disabled @endunless>
                                    <div class="location-list list-group position-absolute w-100 shadow border rounded-3"
                                        role="listbox" hidden style="max-height: 240px; overflow-y: auto; z-index: 1080;">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="postal_code" class="form-control" id="postalCode"
                                        value="{{ old('postal_code') }}" placeholder="Terisi otomatis dari kelurahan"
                                        readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Voucher <span class="text-muted small">(opsional)</span></label>
                                    <input type="text" name="voucher_code" class="form-control"
                                        value="{{ $voucher_code }}" placeholder="contoh: HEMAT10">
                                    <div class="form-text">Voucher diperiksa saat konfirmasi.</div>
                                </div>
                                <div class="col-12 mb-1">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </section>

                        {{-- Langkah 2: Tiket & Add-on --}}
                        <section class="reg-pane" data-step="2" hidden>
                            <h2 class="h6 fw-bold mb-2">Tier Tiket</h2>
                            <div class="tier-selected card border bg-body-secondary p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div>
                                        <strong>{{ $tier->name }}</strong>
                                        <div class="small text-muted">
                                            Fee: Rp {{ number_format($tier->fee, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <strong class="text-primary fs-5">
                                        Rp {{ number_format($tier->price, 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-baseline gap-2 mb-2">
                                <h2 class="h6 fw-bold mb-0">Add-ons</h2>
                                <span class="small text-muted">
                                    <i class="bi bi-toggle-on me-1"></i>Maks. 1 unit / add-on
                                </span>
                            </div>

                            @if ($addons->isNotEmpty())
                                <div class="row row-cols-1 row-cols-sm-2 g-2">
                                    @foreach ($addons as $addon)
                                        <div class="col">
                                            <div class="addon-card card border h-100">
                                                <div class="card-body py-2 px-2">
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input addon-qty" type="checkbox"
                                                            role="switch" id="addon_{{ $addon->id }}"
                                                            name="addons[{{ $addon->id }}]" value="1"
                                                            {{ (int) old('addons.'.$addon->id, 0) === 1 ? 'checked' : '' }}
                                                            data-addon-name="{{ $addon->name }}"
                                                            data-addon-price="{{ $addon->price }}">
                                                        <label class="form-check-label cursor-pointer"
                                                            for="addon_{{ $addon->id }}">
                                                            <span
                                                                class="d-block small fw-semibold text-truncate">{{ $addon->name }}</span>
                                                            <span class="d-block small text-muted">
                                                                Rp {{ number_format($addon->price, 0, ',', '.') }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small mb-0">Tidak ada add-on untuk event ini.</p>
                            @endif
                        </section>

                        {{-- Langkah 3: Konfirmasi --}}
                        <section class="reg-pane" data-step="3" hidden>
                            <h2 class="h6 fw-bold mb-3">Ringkasan Pendaftaran</h2>

                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Nama</span>
                                    <strong id="sumName">-</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Email</span>
                                    <strong id="sumEmail" class="text-break">-</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Tier Tiket</span>
                                    <strong>{{ $tier->name }} — Rp {{ number_format($tier->price, 0, ',', '.') }}</strong>
                                </li>
                            </ul>

                            <div id="sumAddonsWrap" class="mb-3" hidden>
                                <h3 class="h6 fw-bold mb-2">Add-on Terpilih</h3>
                                <ul class="list-group mb-3" id="sumAddons"></ul>
                            </div>

                            <div class="card bg-body-secondary border-0 p-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Subtotal tiket</span>
                                    <span id="sumTier">Rp {{ number_format($tier->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Add-ons</span>
                                    <span id="sumAddonsTotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                    <span>Fee layanan</span>
                                    <span>Rp {{ number_format($tier->fee, 0, ',', '.') }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-bold fs-5">
                                    <span>Total</span>
                                    <span id="sumTotal">Rp {{ number_format($tier->price, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <p class="small text-muted mt-3 mb-0">
                                Dengan menekan tombol di bawah, data pendaftaranmu akan disimpan
                                dan kamu lanjut ke pembayaran.
                            </p>
                        </section>

                        {{-- Navigasi --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary" data-reg="prev" hidden>
                                <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                            </button>

                            <button type="button" class="btn btn-primary ms-auto" data-reg="next">
                                Berikutnya<i class="bi bi-arrow-right ms-1"></i>
                            </button>

                            <button type="submit" class="btn btn-success ms-auto" data-reg="submit" hidden>
                                <i class="bi bi-check-lg me-1"></i>Lanjut ke Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kolom kanan: informasi event --}}
        <div class="col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm reg-event-info">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold text-uppercase text-muted mb-3">
                        <i class="bi bi-info-circle me-1"></i>Informasi Event
                    </h2>

                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex gap-2 mb-3">
                            <i class="bi bi-calendar3 text-primary mt-1"></i>
                            <div>
                                <div class="fw-semibold">Tanggal</div>
                                @if ($event->starts_at)
                                    {{ \Illuminate\Support\Carbon::parse($event->starts_at)->format('d M Y, H:i') }}
                                    @if ($event->ends_at)
                                        <div class="text-muted">
                                            s/d {{ \Illuminate\Support\Carbon::parse($event->ends_at)->format('d M Y, H:i') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">Belum diatur</span>
                                @endif
                            </div>
                        </li>
                        <li class="d-flex gap-2 mb-3">
                            <i class="bi bi-geo-alt text-primary mt-1"></i>
                            <div>
                                <div class="fw-semibold">Lokasi</div>
                                {{ $event->location ?? 'Online' }}
                                @if ($event->venue_details)
                                    <div class="text-muted">{{ $event->venue_details }}</div>
                                @endif
                            </div>
                        </li>
                        <li class="d-flex gap-2 mb-3">
                            <i class="bi bi-ticket-detailed text-primary mt-1"></i>
                            <div>
                                <div class="fw-semibold">Tiket Kamu</div>
                                {{ $tier->name }} — Rp {{ number_format($tier->price, 0, ',', '.') }}
                                <div class="text-muted">Fee: Rp {{ number_format($tier->fee, 0, ',', '.') }}</div>
                            </div>
                        </li>
                        @if ($event->max_registrants)
                            <li class="d-flex gap-2 mb-3">
                                <i class="bi bi-people text-primary mt-1"></i>
                                <div>
                                    <div class="fw-semibold">Kuota</div>
                                    Maksimal {{ number_format($event->max_registrants, 0, ',', '.') }} pendaftar
                                </div>
                            </li>
                        @endif
                    </ul>

                    @if ($event->description)
                        <hr>
                        <h3 class="h6 fw-bold mb-2">Tentang Event</h3>
                        <p class="small text-muted mb-0" style="white-space: pre-line;">{{ \Illuminate\Support\Str::limit($event->description, 320) }}</p>
                    @endif
                </div>

                <div class="card-footer bg-body-secondary border-0 p-3 text-center">
                    <a href="{{ route('public.events.show', $event->slug) }}" class="small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke detail event
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .reg-steps {
        display: flex;
        gap: .5rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .reg-step-item {
        flex: 1;
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .75rem;
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
        font-size: .82rem;
        color: var(--bs-secondary-color);
        transition: border-color .15s ease, background-color .15s ease, color .15s ease;
    }

    .reg-step-item .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        flex: 0 0 26px;
        border-radius: 50%;
        background-color: var(--bs-tertiary-bg);
        font-weight: 700;
    }

    .reg-step-item.active {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), .08);
        color: var(--bs-body-color);
        font-weight: 600;
    }

    .reg-step-item.active .step-number,
    .reg-step-item.completed .step-number {
        background-color: var(--bs-primary);
        color: #fff;
    }

    .reg-step-item.completed {
        color: var(--bs-body-color);
    }

    .reg-pane {
        min-height: 220px;
    }

    .addon-card {
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .addon-card:hover {
        border-color: var(--bs-primary);
    }

    .addon-card:has(.form-check-input:checked) {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), .08);
        box-shadow: 0 0 0 .15rem rgba(var(--bs-primary-rgb), .15);
    }

    /* ---------- Cascading location autocomplete ---------- */
    .location-widget {
        position: relative;
    }

    .location-list {
        top: 100%;
        left: 0;
        margin-top: .25rem;
        background-color: var(--bs-body-bg);
        border-color: var(--bs-border-color);
    }

    .location-list .location-option {
        padding: .4rem .75rem;
        font-size: .875rem;
        cursor: pointer;
    }

    .location-list .location-option:hover,
    .location-list .location-option.active {
        background-color: rgba(var(--bs-primary-rgb), .12);
        color: var(--bs-body-color);
    }

    .location-list .location-empty {
        padding: .5rem .75rem;
        font-size: .8125rem;
        color: var(--bs-secondary-color);
    }

    .location-input:disabled {
        background-color: var(--bs-secondary-bg);
        cursor: not-allowed;
    }

    .reg-event-info {
        position: sticky;
        top: 1rem;
    }

    @media (max-width: 991.98px) {
        .reg-event-info {
            position: static;
        }

        .reg-step-item .step-label {
            display: none;
        }

        .reg-step-item.active .step-label {
            display: inline;
        }
    }
</style>

<script>
    (function () {
        var form = document.getElementById('registrationWizard');
        if (!form) return;

        var panes = Array.prototype.slice.call(form.querySelectorAll('.reg-pane'));
        // Indikator langkah berada di luar elemen form, jadi cari di document.
        var indicators = Array.prototype.slice.call(document.querySelectorAll('[data-step-indicator]'));
        var btnPrev = form.querySelector('[data-reg="prev"]');
        var btnNext = form.querySelector('[data-reg="next"]');
        var btnSubmit = form.querySelector('[data-reg="submit"]');
        var current = 1;
        var last = panes.length;

        form.setAttribute('novalidate', '');

        // Cegah Enter di kolom isian memicu implicit submit — wizard hanya maju lewat tombol.
        form.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') return;

            var tag = event.target.tagName;
            if (tag !== 'INPUT' && tag !== 'SELECT') return;

            event.preventDefault();
        });

        function show(step) {
            current = step;

            panes.forEach(function (pane) {
                pane.hidden = Number(pane.dataset.step) !== step;
            });

            indicators.forEach(function (item) {
                var number = Number(item.dataset.stepIndicator);
                item.classList.toggle('active', number === step);
                item.classList.toggle('completed', number < step);
            });

            btnPrev.hidden = step === 1;
            btnNext.hidden = step === last;
            btnSubmit.hidden = step !== last;

            if (step === last) {
                renderSummary();
            }

            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function validate(step) {
            var pane = panes.filter(function (item) {
                return Number(item.dataset.step) === step;
            })[0];

            if (!pane) return true;

            var fields = pane.querySelectorAll('input, select, textarea');

            for (var i = 0; i < fields.length; i++) {
                if (!fields[i].checkValidity()) {
                    fields[i].reportValidity();
                    return false;
                }
            }

            return true;
        }

        function rupiah(number) {
            return 'Rp ' + Number(number || 0).toLocaleString('id-ID');
        }

        function renderSummary() {
            var name = form.querySelector('[name="full_name"]').value.trim();
            var email = form.querySelector('[name="email"]').value.trim();

            document.getElementById('sumName').textContent = name || '-';
            document.getElementById('sumEmail').textContent = email || '-';

            var addonTotal = 0;
            var addonList = document.getElementById('sumAddons');
            var addonWrap = document.getElementById('sumAddonsWrap');
            addonList.innerHTML = '';

            Array.prototype.forEach.call(form.querySelectorAll('.addon-qty'), function (input) {
                // Switcher hanya punya dua state: aktif (1 unit) atau nonaktif (0).
                if (!input.checked) return;

                var price = Number(input.dataset.addonPrice || 0);
                addonTotal += price;

                var item = document.createElement('li');
                item.className = 'list-group-item d-flex justify-content-between';

                var name = document.createElement('span');
                name.textContent = input.dataset.addonName;

                var amount = document.createElement('strong');
                amount.textContent = rupiah(price);

                item.appendChild(name);
                item.appendChild(amount);
                addonList.appendChild(item);
            });

            addonWrap.hidden = addonList.children.length === 0;

            var tierPrice = Number('{{ $tier->price }}');
            document.getElementById('sumAddonsTotal').textContent = rupiah(addonTotal);
            document.getElementById('sumTotal').textContent = rupiah(tierPrice + addonTotal);
        }

        btnNext.addEventListener('click', function () {
            if (validate(current)) show(current + 1);
        });

        btnPrev.addEventListener('click', function () {
            show(current - 1);
        });

        form.addEventListener('submit', function (event) {
            for (var step = 1; step <= last; step++) {
                if (!validate(step)) {
                    event.preventDefault();
                    show(step);
                    return;
                }
            }
        });

        show(1);
    })();
</script>

<script>
    /**
     * Cascading autocomplete wilayah (Provinsi → Kota/Kab → Kecamatan → Kelurahan).
     * Data diambil dari emsifa static API (https://www.emsifa.com/api-wilayah-indonesia).
     * Ketik untuk memfilter, gunakan ↑/↓ untuk navigasi, Enter untuk memilih, Esc untuk menutup.
     */
    (function () {
        var API_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/v2';
        var MAX_RESULTS = 60;
        var memoryCache = {};
        var pendingFetches = {};

        // Village lists bisa sangat besar, jadi hanya cache di memory; level lain aman di localStorage.
        var LOCAL_CACHE_KEY = 'kafeinarts.wilayah.cache.v2';

        function readLocalCache() {
            try {
                return JSON.parse(localStorage.getItem(LOCAL_CACHE_KEY) || '{}');
            } catch (e) {
                return {};
            }
        }

        function writeLocalCache(key, items) {
            try {
                var cache = readLocalCache();
                cache[key] = items;
                localStorage.setItem(LOCAL_CACHE_KEY, JSON.stringify(cache));
            } catch (e) {
                // localStorage penuh — abaikan, memory cache tetap dipakai.
            }
        }

        function fetchWilayah(path, cacheInStorage) {
            if (memoryCache[path]) {
                return Promise.resolve(memoryCache[path]);
            }

            if (cacheInStorage) {
                var local = readLocalCache();
                if (Array.isArray(local[path])) {
                    memoryCache[path] = local[path];
                    return Promise.resolve(local[path]);
                }
            }

            if (pendingFetches[path]) {
                return pendingFetches[path];
            }

            var promise = fetch(API_BASE + path)
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Wilayah API responded HTTP ' + response.status);
                    }
                    return response.text();
                })
                .then(function (text) {
                    var json = JSON.parse(text);
                    var data = Array.isArray(json) ? json : (json.data || []);
                    var items = data.map(function (item) {
                        return {
                            id: String(item.id),
                            name: item.name,
                            postal_code: item.postal_code || '',
                        };
                    });
                    memoryCache[path] = items;
                    if (cacheInStorage) {
                        writeLocalCache(path, items);
                    }
                    return items;
                })
                .finally(function () {
                    delete pendingFetches[path];
                });

            pendingFetches[path] = promise;
            return promise;
        }

        var LEVELS = [
            { level: 'provinsi', path: function () { return '/provinces.json'; }, useStorage: true, placeholder: 'Ketik untuk mencari provinsi...' },
            { level: 'kota', path: function (parent) { return '/regencies/' + parent.id + '.json'; }, useStorage: true, placeholder: 'Pilih provinsi dulu...' },
            { level: 'kecamatan', path: function (parent) { return '/districts/' + parent.id + '.json'; }, useStorage: true, placeholder: 'Pilih kota / kabupaten dulu...' },
            { level: 'kelurahan', path: function (parent) { return '/villages/' + parent.id + '.json'; }, useStorage: false, placeholder: 'Pilih kecamatan dulu...' },
        ];

        var postalInput = document.getElementById('postalCode');
        var widgets = {};

        Array.prototype.forEach.call(document.querySelectorAll('.location-widget'), function (widget) {
            widgets[widget.dataset.level] = widget;
        });

        function childLevel(level) {
            var index = LEVELS.findIndex(function (item) { return item.level === level; });
            return LEVELS[index + 1] ? LEVELS[index + 1].level : null;
        }

        function resetCascade(fromLevel) {
            var reached = false;

            LEVELS.forEach(function (config) {
                if (config.level === fromLevel) {
                    reached = true;
                    return;
                }

                if (!reached) return;

                var widget = widgets[config.level];
                if (!widget) return;

                var input = widget.querySelector('.location-input');

                input.value = '';
                input.disabled = true;
                input.placeholder = config.placeholder;
                widget._items = null;
                widget._selected = null;
                closeList(widget);
            });

            if (fromLevel === 'provinsi' || fromLevel === 'kota' || fromLevel === 'kecamatan') {
                if (postalInput) postalInput.value = '';
            }
        }

        function closeList(widget) {
            var list = widget.querySelector('.location-list');
            if (list) {
                list.hidden = true;
                list.innerHTML = '';
            }
        }

        function renderList(widget, items, keyword) {
            var list = widget.querySelector('.location-list');
            list.innerHTML = '';

            var filter = (keyword || '').trim().toLowerCase();
            var matched = filter
                ? items.filter(function (item) { return item.name.toLowerCase().indexOf(filter) !== -1; })
                : items.slice();

            widget._filtered = matched;
            widget._activeIndex = -1;

            if (!matched.length) {
                var empty = document.createElement('div');
                empty.className = 'location-empty';
                empty.textContent = 'Tidak ada hasil untuk "' + filter + '"';
                list.appendChild(empty);
                list.hidden = false;
                return;
            }

            matched.slice(0, MAX_RESULTS).forEach(function (item) {
                var option = document.createElement('button');
                option.type = 'button';
                option.className = 'list-group-item list-group-item-action location-option';
                option.setAttribute('role', 'option');
                option.textContent = item.name;
                option.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                    selectItem(widget, item);
                });
                list.appendChild(option);
            });

            if (matched.length > MAX_RESULTS) {
                var more = document.createElement('div');
                more.className = 'location-empty';
                more.textContent = 'Menampilkan ' + MAX_RESULTS + ' dari ' + matched.length + ' hasil — persempit pencarian.';
                list.appendChild(more);
            }

            list.hidden = false;
        }

        function highlightOption(widget, index) {
            var options = widget.querySelectorAll('.location-option');
            options.forEach(function (option, i) {
                option.classList.toggle('active', i === index);
                if (i === index) {
                    option.scrollIntoView({ block: 'nearest' });
                }
            });
            widget._activeIndex = index;
        }

        function selectItem(widget, item) {
            var input = widget.querySelector('.location-input');
            input.value = item.name;
            widget._selected = item;
            closeList(widget);

            var level = widget.dataset.level;
            var child = childLevel(level);

            // Reset semua level di bawah, lalu aktifkan child langsung.
            resetCascade(level);

            if (postalInput && level === 'kelurahan' && item.postal_code) {
                postalInput.value = item.postal_code;
            }

            if (child) {
                var childWidget = widgets[child];
                var childInput = childWidget.querySelector('.location-input');
                childInput.disabled = false;
                childInput.placeholder = 'Ketik untuk mencari ' + child + '...';

                // Prefetch data anak agar muncul cepat saat user fokus.
                loadItems(childWidget).catch(function () {});
            }

            input.focus();
        }

        function isActive(widget) {
            return widget === document.activeElement || widget.contains(document.activeElement);
        }

        function loadItems(widget) {
            if (widget._items) {
                return Promise.resolve(widget._items);
            }

            var level = widget.dataset.level;
            var config = LEVELS.filter(function (entry) { return entry.level === level; })[0];

            var parent = null;
            var index = LEVELS.findIndex(function (entry) { return entry.level === level; });
            if (index > 0) {
                var parentWidget = widgets[LEVELS[index - 1].level];
                parent = parentWidget ? parentWidget._selected : null;
                if (!parent) {
                    return Promise.reject(new Error('parent not selected'));
                }
            }

            if (isActive(widget)) {
                showLoading(widget);
            }

            return fetchWilayah(config.path(parent), config.useStorage)
                .then(function (items) {
                    widget._items = items;
                    return items;
                })
                .catch(function (error) {
                    if (isActive(widget)) {
                        showError(widget);
                    }
                    throw error;
                });
        }

        function showLoading(widget) {
            var list = widget.querySelector('.location-list');
            list.innerHTML = '<div class="location-empty">Memuat data...</div>';
            list.hidden = false;
        }

        function showError(widget) {
            var list = widget.querySelector('.location-list');
            list.innerHTML = '<div class="location-empty text-danger">Gagal memuat data. Periksa koneksi internet lalu coba lagi.</div>';
            list.hidden = false;
        }

        Object.keys(widgets).forEach(function (level) {
            var widget = widgets[level];
            var input = widget.querySelector('.location-input');
            var list = widget.querySelector('.location-list');

            input.addEventListener('focus', function () {
                if (input.disabled) return;
                loadItems(widget).then(function (items) {
                    renderList(widget, items, input.value);
                }).catch(function () {});
            });

            input.addEventListener('input', function () {
                widget._selected = null;

                // Ketikan manual membatalkan pilihan induk — reset level di bawahnya.
                resetCascade(level);

                loadItems(widget).then(function (items) {
                    renderList(widget, items, input.value);
                }).catch(function () {});
            });

            input.addEventListener('keydown', function (event) {
                if (list.hidden) return;

                var filtered = widget._filtered || [];
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    var next = widget._activeIndex < filtered.length - 1 ? widget._activeIndex + 1 : 0;
                    highlightOption(widget, Math.min(next, Math.min(filtered.length, MAX_RESULTS) - 1));
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    var prev = widget._activeIndex > 0 ? widget._activeIndex - 1 : Math.min(filtered.length, MAX_RESULTS) - 1;
                    highlightOption(widget, prev);
                } else if (event.key === 'Enter') {
                    if (widget._activeIndex >= 0 && filtered[widget._activeIndex]) {
                        event.preventDefault();
                        selectItem(widget, filtered[widget._activeIndex]);
                    }
                } else if (event.key === 'Escape') {
                    closeList(widget);
                }
            });

            input.addEventListener('blur', function () {
                // Beri waktu untuk mousedown sebelum list ditutup.
                setTimeout(function () { closeList(widget); }, 150);
            });

            // Klik di luar widget menutup dropdown.
            document.addEventListener('click', function (event) {
                if (!widget.contains(event.target)) {
                    closeList(widget);
                }
            });
        });
    })();
</script>
@endsection
