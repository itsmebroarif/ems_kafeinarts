@props(['event', 'type'])

@php
    $label = \App\Services\MasterData\MasterDataImportExportService::labels()[$type];
    $modalId = 'importModal-'.str_replace('_', '-', $type);
    $exportUrl = route('admin.events.export', [$event, $type]);
    $templateUrl = route('admin.events.export.template', [$event, $type]);
    $importUrl = route('admin.events.import', [$event, $type]);
    $columns = app(\App\Services\MasterData\MasterDataImportExportService::class)->columns($type);
@endphp

<div class="d-flex flex-wrap gap-2" {{ $attributes }}>
    <a href="{{ $exportUrl }}" class="btn btn-outline-success">
        <i class="bi bi-download me-1"></i>Ekspor CSV
    </a>

    <a href="{{ $templateUrl }}" class="btn btn-outline-secondary" title="Unduh format baku">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Template
    </a>

    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
        data-bs-target="#{{ $modalId }}">
        <i class="bi bi-upload me-1"></i>Impor CSV
    </button>
</div>

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ $importUrl }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">
                        <i class="bi bi-upload me-1"></i>Impor {{ $label }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Unggah file CSV dengan kolom:
                        <strong>{{ implode(', ', $columns) }}</strong>.
                        Data yang sudah ada (berdasarkan
                        {{ in_array($type, ['registrants', 'panitia'], true) ? 'email' : 'kode' }}) akan diperbarui,
                        selebihnya ditambahkan sebagai data baru.
                    </p>

                    <div class="mb-3">
                        <label class="form-label">File CSV <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                        <div class="form-text">
                            Maksimal 5 MB. Gunakan tombol <strong>Template</strong> untuk format baku.
                        </div>
                    </div>

                    @error('file')
                        <div class="alert alert-danger py-2 small mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                        </div>
                    @enderror

                    @error('import')
                        <div class="alert alert-danger py-2 small mb-0">
                            <div class="fw-semibold mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Impor dibatalkan:
                            </div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->get('import') as $importError)
                                    <li>{{ $importError }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Mulai Impor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->has('file') || $errors->has('import'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalElement = document.getElementById('{{ $modalId }}');
            if (modalElement && window.bootstrap) {
                new bootstrap.Modal(modalElement).show();
            }
        });
    </script>
@endif
