<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_items', function (Blueprint $table) {
            // Menyimpan data URI PNG base64 yang jauh lebih panjang dari 255 karakter.
            $table->text('qr_code')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ticket_items', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->change();
        });
    }
};
