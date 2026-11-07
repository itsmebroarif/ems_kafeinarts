<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ticket_items')) {
            return;
        }

        Schema::create('ticket_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registrant_id')->constrained('registrants')->cascadeOnDelete();
            $table->foreignUuid('ticket_tier_id')->constrained('event_ticket_tiers')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('qr_code')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_used')->default(false);
            $table->string('barcode_type')->default('qrcode');
            $table->boolean('is_printed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_items');
    }
};
