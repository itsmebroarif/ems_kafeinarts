<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\MasterData\MasterDataImportExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterDataController extends Controller
{
    public function __construct(
        private readonly MasterDataImportExportService $service,
    ) {}

    /**
     * Unduh CSV master data milik sebuah event.
     */
    public function export(Event $event, string $type): StreamedResponse
    {
        $this->guard($type);

        $columns = $this->service->columns($type);
        $records = $this->service->exportRecords($type, $event);
        $filename = $this->filename($event, $type);

        return response()->streamDownload(function () use ($columns, $records) {
            $handle = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8 dengan benar.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $columns, ',', '"', '\\');

            foreach ($records as $record) {
                fputcsv($handle, array_values($record), ',', '"', '\\');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Unduh template CSV (header + satu baris contoh) tanpa data event.
     */
    public function template(Event $event, string $type): StreamedResponse
    {
        $this->guard($type);

        $filename = 'template-'.$type.'.csv';

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $this->service->columns($type), ',', '"', '\\');
            fputcsv($handle, $this->service->sampleRow($type), ',', '"', '\\');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Impor CSV master data: seluruh baris divalidasi, lalu disimpan dalam transaksi.
     */
    public function import(Request $request, Event $event, string $type): RedirectResponse
    {
        $this->guard($type);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $parsed = $this->service->parseFile($request->file('file')->getRealPath());
        $label = MasterDataImportExportService::labels()[$type];

        if ($parsed['columns'] === []) {
            return back()->withErrors(['import' => 'File CSV kosong atau tidak memiliki baris header.']);
        }

        $missing = array_diff($this->service->requiredColumns($type), $parsed['columns']);

        if ($missing !== []) {
            return back()->withErrors([
                'import' => 'Kolom wajib tidak ditemukan: '.implode(', ', $missing)
                    .'. Silakan gunakan template yang tersedia.',
            ]);
        }

        if ($parsed['records'] === []) {
            return back()->withErrors(['import' => 'File CSV tidak berisi data (hanya baris header).']);
        }

        $result = $this->service->import($type, $event, $parsed['records']);

        if ($result['errors'] !== []) {
            return back()->withErrors(['import' => $result['errors']]);
        }

        return redirect()
            ->route($this->indexRoute($type), $event)
            ->with('success', sprintf(
                'Impor %s selesai: %d data baru, %d data diperbarui.',
                $label,
                $result['created'],
                $result['updated'],
            ));
    }

    private function guard(string $type): void
    {
        abort_unless(MasterDataImportExportService::isValidType($type), 404);
    }

    private function filename(Event $event, string $type): string
    {
        return sprintf('%s-%s.csv', $event->slug, $type);
    }

    /**
     * Route index tujuan redirect setelah impor berhasil.
     */
    private function indexRoute(string $type): string
    {
        return match ($type) {
            'tiers' => 'admin.events.tiers.index',
            'addons' => 'admin.events.addons.index',
            'vouchers' => 'admin.events.vouchers.index',
            'registrants' => 'admin.events.registrants.index',
            'panitia' => 'admin.events.panitia.index',
        };
    }
}
