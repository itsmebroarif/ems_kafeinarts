@php
    $event = $event ?? null;
    $categories = $categories ?? collect();

    // Mode "tabs" (edit): langkah bisa dibuka bebas; mode "wizard" (create): urut.
    $mode = $mode ?? 'wizard';
    $wizardSteps = [
        ['number' => 1, 'icon' => 'bi-info-circle', 'label' => 'Info Dasar', 'hint' => 'Nama, kategori & jenis event'],
        ['number' => 2, 'icon' => 'bi-geo-alt', 'label' => 'Lokasi & Waktu', 'hint' => 'Provinsi, kota, tanggal & jam'],
        ['number' => 3, 'icon' => 'bi-cash-coin', 'label' => 'Biaya & Kuota', 'hint' => 'Harga, fee & kuota'],
        ['number' => 4, 'icon' => 'bi-images', 'label' => 'Media & Publikasi', 'hint' => 'Avatar, banner & status publikasi'],
    ];

    $formatDateTime = function ($value) {
        if (! $value) {
            return '';
        }

        return \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i');
    };
@endphp

<div class="card border-0 shadow">
    <div class="card-header bg-body-secondary py-3">
        <h4 class="mb-0">{{ $title }}</h4>
    </div>

    <div class="card-body p-4">
        <form method="POST" action="{{ $action }}" id="eventWizard" enctype="multipart/form-data"
            data-wizard-mode="{{ $mode }}">
            @csrf

            @if ($event)
                @method('PUT')
            @endif

            <div class="row g-4 event-wizard-layout">
                {{-- Navigasi vertikal (kiri) --}}
                <div class="col-md-3 col-lg-3">
                    <nav class="wizard-vnav" aria-label="Navigasi langkah event">
                        <ol class="wizard-vsteps">
                            @foreach ($wizardSteps as $step)
                                <li class="wizard-vstep" data-step-indicator="{{ $step['number'] }}"
                                    @if ($mode === 'tabs') tabindex="0" role="button" title="Buka tab {{ $step['label'] }}" @endif>
                                    <span class="wizard-vstep-number">{{ $step['number'] }}</span>
                                    <span class="wizard-vstep-body">
                                        <span class="wizard-vstep-label">
                                            <i class="bi {{ $step['icon'] }} me-1"></i>{{ $step['label'] }}
                                        </span>
                                        <span class="wizard-vstep-hint">{{ $step['hint'] }}</span>
                                    </span>
                                    <i class="bi bi-chevron-right wizard-vstep-arrow"></i>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                </div>

                {{-- Konten langkah (kanan) --}}
                <div class="col-md-9 col-lg-9 event-wizard-content">

            {{-- Langkah 1: Info Dasar --}}
            <section class="wizard-pane" data-step="1">
                <h5 class="wizard-pane-title"><i class="bi bi-info-circle me-1"></i>Langkah 1 — Info Dasar</h5>

                <div class="mb-3">
                    <label class="form-label">Nama Event <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $event->name ?? '') }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $event->slug ?? '') }}"
                        placeholder="contoh: konser-akhir-tahun" required>
                    <div class="form-text">URL publik: {{ url('/event') }}/nama-slug</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori Event</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-collection"></i></span>
                        <select name="category_id" class="form-select">
                            <option value="">— Tanpa kategori —</option>
                            @foreach ($categories as $category)
                                @php
                                    $isSport = $category->is_sport ?? false;
                                    $selectedCategory = old('category_id', $event->category_id ?? session('selected_category'));
                                @endphp
                                <option value="{{ $category->id }}"
                                    {{ (string) $selectedCategory === (string) $category->id ? 'selected' : '' }}
                                    {{ $isSport ? 'data-sport="true"' : '' }}>
                                    {{ $category->name }}{{ $isSport ? ' - Sport' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#categoryModal" title="Tambah kategori baru">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        Kategori ditampilkan sebagai badge pada daftar event publik.<br>
                        <strong>Catatan:</strong> Kategori dengan <span class="text-success">⚡ Sport</span> boleh dimasukkan ke kolom <strong>Result</strong> pada papan event (kanban).
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Event <span class="text-danger">*</span></label>
                    @php
                        $selectedType = old('event_type', $event->event_type ?? \App\Enums\EventType::KOMPETISI);
                    @endphp
                    <div class="row g-2">
                        @foreach (\App\Enums\EventType::options() as $typeKey => $typeOption)
                            <div class="col-md-6">
                                <label class="event-type-card h-100 w-100 rounded-3 border p-3 d-block"
                                    for="eventType{{ ucfirst($typeKey) }}">
                                    <div class="d-flex align-items-start gap-2">
                                        <input type="radio" name="event_type" id="eventType{{ ucfirst($typeKey) }}"
                                            class="form-check-input mt-1" value="{{ $typeKey }}" required
                                            {{ $selectedType === $typeKey ? 'checked' : '' }}>
                                        <div>
                                            <div class="fw-semibold">
                                                <i class="bi {{ $typeOption['icon'] }} text-{{ $typeOption['color'] }} me-1"></i>
                                                {{ $typeOption['label'] }}
                                            </div>
                                            <div class="form-text mt-0">{{ $typeOption['hint'] }}</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">
                        Menentukan apakah event ini berupa kompetisi (lomba/turnamen) atau event hiburan.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="5">{{ old('description', $event->description ?? '') }}</textarea>
                </div>
            </section>

            {{-- Langkah 2: Lokasi & Waktu --}}
            <section class="wizard-pane" data-step="2">
                <h5 class="wizard-pane-title"><i class="bi bi-geo-alt me-1"></i>Langkah 2 — Lokasi & Waktu</h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <select name="provinsi" class="form-select" id="provinsiSelect" required>
                            <option value="">— Pilih Provinsi —</option>
                            <option value="Aceh">Aceh</option>
                            <option value="Sumatera Utara">Sumatera Utara</option>
                            <option value="Sumatera Barat">Sumatera Barat</option>
                            <option value="Riau">Riau</option>
                            <option value="Kepulauan Riau">Kepulauan Riau</option>
                            <option value="Jambi">Jambi</option>
                            <option value="Bengkulu">Bengkulu</option>
                            <option value="Sumatera Selatan">Sumatera Selatan</option>
                            <option value="Bangka Belitung">Bangka Belitung</option>
                            <option value="Lampung">Lampung</option>
                            <option value="Banten">Banten</option>
                            <option value="DKI Jakarta">DKI Jakarta</option>
                            <option value="Jawa Barat">Jawa Barat</option>
                            <option value="Jawa Tengah">Jawa Tengah</option>
                            <option value="Yogyakarta">Yogyakarta</option>
                            <option value="Jawa Timur">Jawa Timur</option>
                            <option value="Kalimantan Barat">Kalimantan Barat</option>
                            <option value="Kalimantan Tengah">Kalimantan Tengah</option>
                            <option value="Kalimantan Selatan">Kalimantan Selatan</option>
                            <option value="Kalimantan Timur">Kalimantan Timur</option>
                            <option value="Kalimantan Utara">Kalimantan Utara</option>
                            <option value="Sulawesi Utara">Sulawesi Utara</option>
                            <option value="Sulawesi Tengah">Sulawesi Tengah</option>
                            <option value="Sulawesi Tenggara">Sulawesi Tenggara</option>
                            <option value="Sulawesi Selatan">Sulawesi Selatan</option>
                            <option value="Sulawesi Barat">Sulawesi Barat</option>
                            <option value="Gorontalo">Gorontalo</option>
                            <option value="Maluku">Maluku</option>
                            <option value="Maluku Utara">Maluku Utara</option>
                            <option value="Papua">Papua</option>
                            <option value="Papua Barat">Papua Barat</option>
                            <option value="Papua Selatan">Papua Selatan</option>
                            <option value="Papua Tengah">Papua Tengah</option>
                            <option value="Papua Pegunungan">Papua Pegunungan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kota / Kabupaten <span class="text-danger">*</span></label>
                        <select name="city" class="form-select" id="citySelect" required disabled data-old="{{ old('city', $event->city ?? '') }}">
                            <option value="">— Pilih Provinsi terlebih dahulu —</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lokasi Detail</label>
                    <input type="text" name="location" class="form-control"
                        placeholder="contoh: Gedung Serbaguna, Jl. Merdeka No. 1"
                        value="{{ old('location', $event->location ?? '') }}">
                    <div class="form-text">Alamat lengkap venue event.</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="starts_at" class="form-control"
                            value="{{ old('starts_at', $formatDateTime($event->starts_at ?? null) ? \Illuminate\Support\Carbon::parse($event->starts_at)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai <span class="text-muted small">(opsional, 2 hari)</span></label>
                        <input type="date" name="ends_at" class="form-control"
                            value="{{ old('ends_at', isset($event->ends_at) ? \Illuminate\Support\Carbon::parse($event->ends_at)->format('Y-m-d') : '') }}">
                        <div class="form-text">Kosongkan jika hanya 1 hari.</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Waktu Mulai</label>
                        <input type="time" name="starts_time" class="form-control"
                            value="{{ old('starts_time', $formatDateTime($event->starts_at ?? null) ? \Illuminate\Support\Carbon::parse($event->starts_at)->format('H:i') : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="time" name="ends_time" class="form-control"
                            value="{{ old('ends_time', $formatDateTime($event->ends_at ?? null) ? \Illuminate\Support\Carbon::parse($event->ends_at)->format('H:i') : '') }}">
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Detail Venue</label>
                    <textarea name="venue_details" class="form-control" rows="4">{{ old('venue_details', $event->venue_details ?? '') }}</textarea>
                    <div class="form-text">Informasi tambahan tentang venue: parkir, transportasi umum, fasilitas, dll.</div>
                </div>
            </section>

            {{-- Langkah 3: Biaya & Kuota --}}
            <section class="wizard-pane" data-step="3">
                <h5 class="wizard-pane-title"><i class="bi bi-cash-coin me-1"></i>Langkah 3 — Biaya & Kuota</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Dasar (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" min="0" step="1"
                            value="{{ old('price', $event->price ?? 0) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Quota Maksimal</label>
                        <input type="number" name="max_registrants" class="form-control" min="0"
                            value="{{ old('max_registrants', $event->max_registrants ?? 0) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Biaya Admin (Rp)</label>
                        <input type="number" name="fee" class="form-control" min="0" step="1"
                            value="{{ old('fee', $event->fee ?? 0) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Biaya Pembayaran (Rp)</label>
                        <input type="number" name="submit_fee" class="form-control" min="0" step="1"
                            value="{{ old('submit_fee', $event->submit_fee ?? 0) }}">
                    </div>
                </div>
            </section>

            {{-- Langkah 4: Media & Publikasi --}}
            <section class="wizard-pane" data-step="4">
                <h5 class="wizard-pane-title"><i class="bi bi-images me-1"></i>Langkah 4 — Media & Publikasi</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Avatar Event</label>
                        <div class="mb-2">
                            <div class="avatar-preview d-inline-flex align-items-center justify-content-center overflow-hidden"
                                id="logoPreviewTarget">
                                @if ($event && $event->logo_url)
                                    <img src="{{ $event->logo_url }}" alt="Avatar {{ $event->name }}"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="bi bi-person fs-4 text-muted"></i>
                                @endif
                            </div>
                        </div>
                        <input type="file" name="logo" class="form-control" accept="image/*"
                            data-preview-target="logoPreviewTarget">
                        <div class="form-text">JPG/PNG/WEBP, maks 2 MB. Kosongkan untuk memakai avatar SVG default.</div>

                        @if ($event && $event->logo)
                            <div class="form-check mt-1">
                                <input type="checkbox" name="remove_logo" value="1" class="form-check-input"
                                    id="removeLogo">
                                <label class="form-check-label small" for="removeLogo">Hapus avatar saat ini</label>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Banner Event</label>
                        <div class="mb-2">
                            <div class="media-preview d-flex align-items-center justify-content-center"
                                id="bannerPreviewTarget">
                                @if ($event && $event->banner_url)
                                    <img src="{{ $event->banner_url }}" alt="Banner {{ $event->name }}"
                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: .6rem;">
                                @else
                                    <span class="text-muted"><i class="bi bi-image me-1"></i>Belum ada banner — akan
                                        digenerate SVG otomatis</span>
                                @endif
                            </div>
                        </div>
                        <input type="file" name="banner" class="form-control" accept="image/*"
                            data-preview-target="bannerPreviewTarget">
                        <div class="form-text">JPG/PNG/WEBP, maks 4 MB. Rasio disarankan 16:5.</div>

                        @if ($event && $event->banner)
                            <div class="form-check mt-1">
                                <input type="checkbox" name="remove_banner" value="1" class="form-check-input"
                                    id="removeBanner">
                                <label class="form-check-label small" for="removeBanner">Hapus banner saat ini</label>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_published" value="1" class="form-check-input" id="isPublished"
                            {{ old('is_published', $event->is_published ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isPublished">Tersedia untuk publik</label>
                    </div>
                </div>
            </section>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn btn-outline-secondary" data-wizard="prev" hidden>
                    <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                </button>

                <button type="button" class="btn btn-primary ms-auto" data-wizard="next">
                    Berikutnya<i class="bi bi-arrow-right ms-1"></i>
                </button>

                <button type="submit" class="btn btn-success ms-auto" data-wizard="submit" hidden>
                    <i class="bi bi-check-lg me-1"></i>{{ $submitLabel }}
                </button>
            </div>
        </div>{{-- /event-wizard-content --}}
        </div>{{-- /event-wizard-layout --}}
        </form>
    </div>
</div>

{{-- Modal tambah kategori baru (di luar form utama agar tidak bersarang) --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Kategori Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="category_name" class="form-control" placeholder="contoh: Webinar"
                        required autofocus>
                </div>

                <div class="mb-1">
                    <label class="form-label">Warna Badge</label>
                    <input type="color" name="color" class="form-control form-control-color w-100"
                        value="#0d6efd" style="height: 42px;">
                    <div class="form-text">Dipakai sebagai warna badge pada halaman event publik.</div>
                </div>

                <div class="form-check mt-3">
                    <input type="checkbox" name="is_sport" value="1" class="form-check-input" id="categoryIsSport"
                        {{ old('is_sport') ? 'checked' : '' }}>
                    <label class="form-check-label" for="categoryIsSport">
                        Kategori sport / esport
                    </label>
                    <div class="form-text mt-0">
                        Event dengan kategori ini bisa dipindah ke kolom <strong>Result</strong> pada papan event.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

@if ($errors->has('category_name') || $errors->has('color'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalElement = document.getElementById('categoryModal');
            if (modalElement && window.bootstrap) {
                new bootstrap.Modal(modalElement).show();
            }
        });
    </script>
@endif

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('eventWizard');
            if (!form) return;

            var mode = form.getAttribute('data-wizard-mode') || 'wizard';
            var isTabs = mode === 'tabs';

            var panes = Array.prototype.slice.call(form.querySelectorAll('.wizard-pane'));
            var indicators = Array.prototype.slice.call(form.querySelectorAll('[data-step-indicator]'));
            var btnPrev = form.querySelector('[data-wizard="prev"]');
            var btnNext = form.querySelector('[data-wizard="next"]');
            var btnSubmit = form.querySelector('[data-wizard="submit"]');
            var current = 1;

            form.setAttribute('novalidate', '');

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

                if (isTabs) {
                    // Mode tab: submit selalu tampil, navigasi bebas.
                    btnPrev.hidden = step === 1;
                    btnNext.hidden = true;
                    btnSubmit.hidden = false;
                } else {
                    btnPrev.hidden = step === 1;
                    btnNext.hidden = step === panes.length;
                    btnSubmit.hidden = step !== panes.length;
                }
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

            btnNext.addEventListener('click', function () {
                if (validate(current)) show(current + 1);
            });

            btnPrev.addEventListener('click', function () {
                show(current - 1);
            });

            // Mode tab: klik indikator langkah untuk berpindah bebas.
            if (isTabs) {
                indicators.forEach(function (item) {
                    var jump = function () {
                        show(Number(item.dataset.stepIndicator));
                    };

                    item.addEventListener('click', jump);
                    item.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            jump();
                        }
                    });
                });
            }

            form.addEventListener('submit', function (event) {
                for (var step = 1; step <= panes.length; step++) {
                    if (!validate(step)) {
                        event.preventDefault();
                        show(step);
                        return;
                    }
                }
            });

            Array.prototype.forEach.call(form.querySelectorAll('input[type="file"][data-preview-target]'), function (input) {
                input.addEventListener('change', function () {
                    var file = input.files && input.files[0];
                    var target = document.getElementById(input.dataset.previewTarget);
                    if (!file || !target) return;

                    var reader = new FileReader();
                    reader.onload = function (loadEvent) {
                        target.innerHTML = '';
                        var image = document.createElement('img');
                        image.src = loadEvent.target.result;
                        image.alt = 'Pratinjau';
                        image.style.cssText = 'width:100%;height:100%;object-fit:cover;' +
                            (target.classList.contains('avatar-preview') ? 'border-radius:50%;' : 'border-radius:.6rem;');
                        target.appendChild(image);
                    };
                    reader.readAsDataURL(file);
                });
            });

            show(1);
        })();
    </script>
@endpush

@push('scripts')
<script>
(function () {
    // Data provinsi dan kota/kabupaten utama di Indonesia
    // Sumber: BPS / Kementerian Dalam Negeri (disederhanakan untuk keperluan form)
    var cityData = {
        'Aceh': ['Banda Aceh', 'Kota Langsa', 'Kota Lhokseumawe', 'Kota Sabang', 'Kota Subulussalam', 'Kabupaten Aceh Besar', 'Kabupaten Aceh Selatan', 'Kabupaten Aceh Singkil', 'Kabupaten Aceh Tamiang', 'Kabupaten Aceh Tenggara', 'Kabupaten Aceh Timur', 'Kabupaten Aceh Utara', 'Kabupaten Bener Meriah', 'Kabupaten Gayo Lues', 'Kabupaten Nagan Raya', 'Kabupaten Aceh Barat', 'Kabupaten Aceh Barat Daya', 'Kabupaten Aceh Jaya', 'Kabupaten Simeulue'],
        'Sumatera Utara': ['Medan', 'Kota Binjai', 'Kota Gunungsitoli', 'Kota Padangsidempuan', 'Kota Sibolga', 'Kota Tanjungbalai', 'Kota Tebing Tinggi', 'Kabupaten Asahan', 'Kabupaten Deli Serdang', 'Kabupaten Karo', 'Kabupaten Langkat', 'Kabupaten Mandailing Natal', 'Kabupaten Nias', 'Kabupaten Nias Utara', 'Kabupaten Nias Barat', 'Kabupaten Nias Selatan', 'Kabupaten Padang Lawas', 'Kabupaten Padang Lawas Utara', 'Kabupaten Pakpak Bharat', 'Kabupaten Serdang Bedagai', 'Kabupaten Simalungun', 'Kabupaten Tapanuli Selatan', 'Kabupaten Tapanuli Tengah', 'Kabupaten Tapanuli Utara', 'Kabupaten Toba', 'Kabupaten Batu Bara'],
        'Sumatera Barat': ['Padang', 'Kota Bukittinggi', 'Kota Padangpanjang', 'Kota Pariaman', 'Kota Payakumbuh', 'Kota Solok', 'Kabupaten Agam', 'Kabupaten Dharmasraya', 'Kabupaten Kepulauan Mentawai', 'Kabupaten Lima Puluh Kota', 'Kabupaten Padang Lawas', 'Kabupaten Pasaman', 'Kabupaten Pasaman Barat', 'Kabupaten Pesisir Selatan', 'Kabupaten Sijunjung', 'Kabupaten Solok', 'Kabupaten Tanah Datar', 'Kabupaten Padang Pariaman'],
        'Riau': ['Pekanbaru', 'Kota Dumai', 'Kabupaten Bengkalis', 'Kabupaten Indragiri Hilir', 'Kabupaten Indragiri Hulu', 'Kabupaten Kampar', 'Kabupaten Kepulauan Meranti', 'Kabupaten Kuantan Singingi', 'Kabupaten Pelalawan', 'Kabupaten Rokan Hilir', 'Kabupaten Rokan Hulu', 'Kabupaten Siak'],
        'Kepulauan Riau': ['Batam', 'Kota Tanjungpinang', 'Kabupaten Bintan', 'Kabupaten Karimun', 'Kabupaten Kepulauan Anambas', 'Kabupaten Lingga', 'Kabupaten Natuna'],
        'Jambi': ['Jambi', 'Kota Sungai Penuh', 'Kabupaten Batanghari', 'Kabupaten Bungo', 'Kabupaten Kerinci', 'Kabupaten Merangin', 'Kabupaten Muaro Jambi', 'Kabupaten Sarolangun', 'Kabupaten Tanjung Jabung Barat', 'Kabupaten Tanjung Jabung Timur', 'Kabupaten Tebo'],
        'Bengkulu': ['Bengkulu', 'Kota Bengkulu', 'Kabupaten Bengkulu Utara', 'Kabupaten Bengkulu Selatan', 'Kabupaten Bengkulu Tengah', 'Kabupaten Kaur', 'Kabupaten Kepahiang', 'Kabupaten Lebong', 'Kabupaten Mukomuko', 'Kabupaten Rejang Lebong', 'Kabupaten Seluma'],
        'Sumatera Selatan': ['Palembang', 'Kota Lubuk Linggau', 'Kota Pagar Alam', 'Kota Prabumulih', 'Kabupaten Banyuasin', 'Kabupaten Empat Lawang', 'Kabupaten Lahat', 'Kabupaten Muara Enim', 'Kabupaten Musi Banyuasin', 'Kabupaten Musi Rawas', 'Kabupaten Musi Rawas Utara', 'Kabupaten Ogan Ilir', 'Kabupaten Ogan Komering Ilir', 'Kabupaten Ogan Komering Ulu', 'Kabupaten Ogan Komering Ulu Selatan', 'Kabupaten Ogan Komering Ulu Timur', 'Kabupaten Penukal Abab Lematang Ilir (Pali)'],
        'Bangka Belitung': ['Pangkal Pinang', 'Kabupaten Bangka', 'Kabupaten Bangka Barat', 'Kabupaten Bangka Tengah', 'Kabupaten Bangka Selatan', 'Kabupaten Belitung', 'Kabupaten Belitung Timur'],
        'Lampung': ['Bandar Lampung', 'Metro', 'Kabupaten Lampung Selatan', 'Kabupaten Lampung Tengah', 'Kabupaten Lampung Timur', 'Kabupaten Lampung Utara', 'Kabupaten Mesuji', 'Kabupaten Pesawaran', 'Kabupaten Pesisir Barat', 'Kabupaten Pringsewu', 'Kabupaten Tanggamus', 'Kabupaten Tulang Bawang', 'Kabupaten Tulang Bawang Barat', 'Kabupaten Way Kanan', 'Kabupaten Lampung Barat'],
        'Banten': ['Serang', 'Cilegon', 'Kota Serang', 'Kota Tangerang', 'Kota Tangerang Selatan', 'Kabupaten Tangerang', 'Kabupaten Lebak', 'Kabupaten Pandeglang', 'Kabupaten Serang'],
        'DKI Jakarta': ['Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat', 'Jakarta Utara', 'Jakarta Pusat', 'Kepulauan Seribu'],
        'Jawa Barat': ['Bandung', 'Kota Cimahi', 'Kota Bekasi', 'Kota Bogor', 'Kota Cirebon', 'Kota Depok', 'Kota Sukabumi', 'Kota Tasikmalaya', 'Kota Banjar', 'Kabupaten Bandung', 'Kabupaten Bandung Barat', 'Kabupaten Bekasi', 'Kabupaten Bogor', 'Kabupaten Ciamis', 'Kabupaten Cianjur', 'Kabupaten Cirebon', 'Kabupaten Garut', 'Kabupaten Indramayu', 'Kabupaten Karawang', 'Kabupaten Kuningan', 'Kabupaten Majalengka', 'Kabupaten Purwakarta', 'Kabupaten Subang', 'Kabupaten Sukabumi', 'Kabupaten Sumedang', 'Kabupaten Tasikmalaya', 'Kabupaten Pangandaran'],
        'Jawa Tengah': ['Semarang', 'Kota Surakarta (Solo)', 'Kota Magelang', 'Kota Pekalongan', 'Kota Salatiga', 'Kota Tegal', 'Kabupaten Banjarnegara', 'Kabupaten Banyumas', 'Kabupaten Batang', 'Kabupaten Blora', 'Kabupaten Boyolali', 'Kabupaten Brebes', 'Kabupaten Cilacap', 'Kabupaten Demak', 'Kabupaten Grobogan', 'Kabupaten Jepara', 'Kabupaten Karanganyar', 'Kabupaten Kebumen', 'Kabupaten Kendal', 'Kabupaten Klaten', 'Kabupaten Kudus', 'Kabupaten Magelang', 'Kabupaten Pati', 'Kabupaten Pekalongan', 'Kabupaten Pemalang', 'Kabupaten Purbalingga', 'Kabupaten Purworejo', 'Kabupaten Rembang', 'Kabupaten Salatiga', 'Kabupaten Semarang', 'Kabupaten Temanggung', 'Kabupaten Wonogiri', 'Kabupaten Wonosobo'],
        'Yogyakarta': ['Kota Yogyakarta', 'Kabupaten Bantul', 'Kabupaten Gunungkidul', 'Kabupaten Kulonprogo', 'Kabupaten Sleman'],
        'Jawa Timur': ['Surabaya', 'Kota Malang', 'Kota Pasuruan', 'Kota Probolinggo', 'Kota Kediri', 'Kota Madiun', 'Kota Blitar', 'Kota Mojokerto', 'Kota Batu', 'Kabupaten Banyuwangi', 'Kabupaten Blitar', 'Kabupaten Bojonegoro', 'Kabupaten Bondowoso', 'Kabupaten Gresik', 'Kabupaten Jember', 'Kabupaten Jombang', 'Kabupaten Kediri', 'Kabupaten Lamongan', 'Kabupaten Lumajang', 'Kabupaten Madiun', 'Kabupaten Magetan', 'Kabupaten Malang', 'Kabupaten Mojokerto', 'Kabupaten Nganjuk', 'Kabupaten Ngawi', 'Kabupaten Pacitan', 'Kabupaten Pamekasan', 'Kabupaten Pasuruan', 'Kabupaten Ponorogo', 'Kabupaten Probolinggo', 'Kabupaten Sampang', 'Kabupaten Sidoarjo', 'Kabupaten Situbondo', 'Kabupaten Sumenep', 'Kabupaten Trenggalek', 'Kabupaten Tuban', 'Kabupaten Tulungagung'],
        'Kalimantan Barat': ['Pontianak', 'Kota Singkawang', 'Kabupaten Bengkayang', 'Kabupaten Kapuas Hulu', 'Kabupaten Kayong Utara', 'Kabupaten Ketapang', 'Kabupaten Kubu Raya', 'Kabupaten Landak', 'Kabupaten Melawi', 'Kabupaten Mempawah', 'Kabupaten Sintang', 'Kabupaten Sekadau', 'Kabupaten Sanggau', 'Kabupaten Sambas'],
        'Kalimantan Tengah': ['Palangkaraya', 'Kabupaten Barito Selatan', 'Kabupaten Barito Timur', 'Kabupaten Barito Utara', 'Kabupaten Gunung Mas', 'Kabupaten Kapuas', 'Kabupaten Katingan', 'Kabupaten Kotawaringin Barat', 'Kabupaten Kotawaringin Timur', 'Kabupaten Lamandau', 'Kabupaten Murung Raya', 'Kabupaten Pulang Pisau', 'Kabupaten Sukamara'],
        'Kalimantan Selatan': ['Banjarmasin', 'Kota Banjarbaru', 'Kabupaten Balangan', 'Kabupaten Banjar', 'Kabupaten Barito Kuala', 'Kabupaten Hulu Sungai Selatan', 'Kabupaten Hulu Sungai Tengah', 'Kabupaten Hulu Sungai Utara', 'Kabupaten Kotabaru', 'Kabupaten Tabalong', 'Kabupaten Tanah Bumbu', 'Kabupaten Tanah Laut', 'Kabupaten Tapin'],
        'Kalimantan Timur': ['Samarinda', 'Balikpapan', 'Kota Bontang', 'Kabupaten Berau', 'Kabupaten Kutai Barat', 'Kabupaten Kutai Kartanegara', 'Kabupaten Kutai Timur', 'Kabupaten Mahakam Ulu', 'Kabupaten Paser', 'Kabupaten Penajam Paser Utara'],
        'Kalimantan Utara': ['Tanjung Selor', 'Kota Tarakan', 'Kabupaten Bulungan', 'Kabupaten Malinau', 'Kabupaten Nunukan', 'Kabupaten Tana Tidung'],
        'Sulawesi Utara': ['Manado', 'Kota Bitung', 'Kota Kotamobagu', 'Kota Tomohon', 'Kabupaten Bolaang Mongondow', 'Kabupaten Bolaang Mongondow Selatan', 'Kabupaten Bolaang Mongondow Timur', 'Kabupaten Bolaang Mongondow Utara', 'Kabupaten Kepulauan Sangihe', 'Kabupaten Kepulauan Talaud', 'Kabupaten Minahasa', 'Kabupaten Minahasa Selatan', 'Kabupaten Minahasa Tenggara', 'Kabupaten Minahasa Utara'],
        'Sulawesi Tengah': ['Palu', 'Kabupaten Banggai', 'Kabupaten Banggai Kepulauan', 'Kabupaten Banggai Laut', 'Kabupaten Parigi Moutong', 'Kabupaten Poso', 'Kabupaten Sigi', 'Kabupaten Tojo Una-Una', 'Kabupaten Donggala', 'Kabupaten Morowali', 'Kabupaten Morowali Utara', 'Kabupaten Toli-Toli', 'Kabupaten Buol'],
        'Sulawesi Tenggara': ['Kendari', 'Kota Bau-Bau', 'Kabupaten Bombana', 'Kabupaten Buton', 'Kabupaten Buton Selatan', 'Kabupaten Buton Tengah', 'Kabupaten Buton Utara', 'Kabupaten Kolaka', 'Kabupaten Kolaka Utara', 'Kabupaten Kolaka Timur', 'Kabupaten Konawe', 'Kabupaten Konawe Kepulauan', 'Kabupaten Konawe Selatan', 'Kabupaten Muna', 'Kabupaten Muna Barat', 'Kabupaten Wakatobi'],
        'Sulawesi Selatan': ['Makassar', 'Kota Palopo', 'Kota Parepare', 'Kabupaten Bantaeng', 'Kabupaten Barru', 'Kabupaten Bone', 'Kabupaten Bulukumba', 'Kabupaten Enrekang', 'Kabupaten Gowa', 'Kabupaten Jeneponto', 'Kabupaten Luwu', 'Kabupaten Luwu Utara', 'Kabupaten Luwu Timur', 'Kabupaten Maros', 'Kabupaten Pangkep', 'Kabupaten Pinrang', 'Kabupaten Sidenreng Rappang (Sidrap)', 'Kabupaten Sinjai', 'Kabupaten Soppeng', 'Kabupaten Takalar', 'Kabupaten Tana Toraja', 'Kabupaten Toraja Utara', 'Kabupaten Wajo'],
        'Sulawesi Barat': ['Mamuju', 'Kota Mamuju', 'Kabupaten Majene', 'Kabupaten Mamasa', 'Kabupaten Pasangkayu', 'Kabupaten Polewali Mandar'],
        'Gorontalo': ['Gorontalo', 'Kota Gorontalo', 'Kabupaten Boalemo', 'Kabupaten Bone Bolango', 'Kabupaten Gorontalo', 'Kabupaten Gorontalo Utara', 'Kabupaten Pohuwato'],
        'Maluku': ['Ambon', 'Kota Ambon', 'Kabupaten Buru', 'Kabupaten Buru Selatan', 'Kabupaten Kepulauan Aru', 'Kabupaten Maluku Barat Daya', 'Kabupaten Maluku Tengah', 'Kabupaten Maluku Tenggara', 'Kabupaten Maluku Tenggara Barat', 'Kabupaten Seram Bagian Barat', 'Kabupaten Seram Bagian Timur'],
        'Maluku Utara': ['Ternate', 'Kota Ternate', 'Kota Tidore Kepulauan', 'Kabupaten Halmahera Barat', 'Kabupaten Halmahera Tengah', 'Kabupaten Halmahera Utara', 'Kabupaten Halmahera Selatan', 'Kabupaten Kepulauan Sula', 'Kabupaten Pulau Morotai', 'Kabupaten Pulau Taliabu'],
        'Papua': ['Jayapura', 'Kota Jayapura', 'Kabupaten Biak Numfor', 'Kabupaten Jayapura', 'Kabupaten Jayawijaya', 'Kabupaten Keerom', 'Kabupaten Kepulauan Yapen', 'Kabupaten Mamberamo Raya', 'Kabupaten Sarmi', 'Kabupaten Waropen', 'Kabupaten Yahukimo', 'Kabupaten Yalimo', 'Kabupaten Paniai', 'Kabupaten Puncak', 'Kabupaten Puncak Jaya', 'Kabupaten Dogiyai', 'Kabupaten Deiyai', 'Kabupaten Intan Jaya', 'Kabupaten Mimika'],
        'Papua Barat': ['Manokwari', 'Kota Manokwari', 'Kabupaten Fakfak', 'Kabupaten Kaimana', 'Kabupaten Manokwari', 'Kabupaten Manokwari Selatan', 'Kabupaten Pegunungan Arfak', 'Kabupaten Teluk Bintuni', 'Kabupaten Teluk Wondama', 'Kabupaten Sorong', 'Kabupaten Sorong Selatan'],
        'Papua Selatan': ['Merauke', 'Kabupaten Merauke', 'Kabupaten Asmat', 'Kabupaten Boven Digoel', 'Kabupaten Mappi', 'Kabupaten Mimika', 'Kabupaten Deiyai', 'Kabupaten Dogiyai', 'Kabupaten Intan Jaya', 'Kabupaten Puncak', 'Kabupaten Puncak Jaya', 'Kabupaten Yahukimo', 'Kabupaten Yalimo', 'Kabupaten Jayawijaya', 'Kabupaten Lanny Jaya', 'Kabupaten Memberamo Tengah', 'Kabupaten Memberamo Hulu'],
        'Papua Tengah': ['Nabire', 'Kabupaten Nabire', 'Kabupaten Paniai', 'Kabupaten Puncak', 'Kabupaten Puncak Jaya', 'Kabupaten Dogiyai', 'Kabupaten Deiyai', 'Kabupaten Intan Jaya', 'Kabupaten Mimika', 'Kabupaten Jayawijaya', 'Kabupaten Lanny Jaya', 'Kabupaten Memberamo Tengah', 'Kabupaten Yahukimo', 'Kabupaten Yalimo'],
        'Papua Pegunungan': ['Wamena', 'Kabupaten Jayawijaya', 'Kabupaten Lanny Jaya', 'Kabupaten Memberamo Tengah', 'Kabupaten Memberamo Hulu', 'Kabupaten Nduga', 'Kabupaten Intan Jaya', 'Kabupaten Puncak', 'Kabupaten Puncak Jaya', 'Kabupaten Dogiyai', 'Kabupaten Deiyai', 'Kabupaten Yahukimo', 'Kabupaten Yalimo']
    };

    var provinsiSelect = document.getElementById('provinsiSelect');
    var citySelect = document.getElementById('citySelect');

    if (!provinsiSelect || !citySelect) return;

    var oldCity = citySelect.getAttribute('data-old') || '';

    function populateCities(selectedProvinsi) {
        citySelect.innerHTML = '<option value="">— Pilih Kota / Kabupaten —</option>';
        citySelect.disabled = !selectedProvinsi;

        var cities = cityData[selectedProvinsi] || [];
        cities.forEach(function (city) {
            var option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });

        if (oldCity && cityData[selectedProvinsi] && cityData[selectedProvinsi].indexOf(oldCity) !== -1) {
            citySelect.value = oldCity;
        }
    }

    provinsiSelect.addEventListener('change', function () {
        populateCities(this.value);
    });

    if (provinsiSelect.value) {
        var oldCityValue = citySelect.getAttribute('data-old') || '';
        populateCities(provinsiSelect.value);
        if (oldCityValue) {
            citySelect.value = oldCityValue;
        }
    }
})();
</script>
@endpush
