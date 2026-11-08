<?php

namespace App\Console\Commands;

use App\Services\ParticipantViewService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('events:sync-participant-views')]
#[Description('Rebuild SQL view peserta_<slug> untuk semua event + hapus view yatim (event tidak ada)')]
class SyncParticipantViews extends Command
{
    public function handle(ParticipantViewService $service): int
    {
        $result = $service->syncAll();

        $this->components->info("{$result['synced']} view peserta dibuat/diperbarui.");

        if ($result['dropped'] > 0) {
            $this->components->warn("{$result['dropped']} view yatim (event tidak ada) dihapus.");
        }

        return self::SUCCESS;
    }
}
