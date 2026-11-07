<?php

use App\Models\Event;
use App\Services\ParticipantViewService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $service = app(ParticipantViewService::class);

        foreach (Event::query()->cursor() as $event) {
            $service->sync($event);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (Event::query()->cursor() as $event) {
            DB::statement('DROP VIEW IF EXISTS `'.app(ParticipantViewService::class)->viewName($event->slug).'`');
        }
    }
};
