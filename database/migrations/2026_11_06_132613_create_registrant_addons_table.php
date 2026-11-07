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
        if (Schema::hasTable('registrant_addons')) {
            return;
        }

        Schema::create('registrant_addons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('registrant_id')->constrained('registrants')->cascadeOnDelete();
            $table->foreignUuid('addon_id')->constrained('event_addons')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('fee', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrant_addons');
    }
};
