<?php

namespace App\Console\Commands;

use App\Services\ParticipantViewService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('events:sync-participant-views')]
#[Description('Rebuild SQL view peserta_<slug> untuk semua event')]
class SyncParticipantViews extends Command
{
    public function handle(ParticipantViewService $service): int
    {
        $count = $service->syncAll();

        $this->components->info("{$count} view peserta berhasil dibuat/diperbarui.");

        return self::SUCCESS;
    }
}
