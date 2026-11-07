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
        Schema::table('registrants', function (Blueprint $table) {
            if (! Schema::hasColumn('registrants', 'session_key')) {
                $table->string('session_key')->nullable()->index();
            }

            if (! Schema::hasColumn('registrants', 'participant_token')) {
                $table->string('participant_token')->nullable()->unique();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            if (Schema::hasColumn('registrants', 'participant_token')) {
                $table->dropUnique(['participant_token']);
                $table->dropColumn('participant_token');
            }

            if (Schema::hasColumn('registrants', 'session_key')) {
                $table->dropIndex(['session_key']);
                $table->dropColumn('session_key');
            }
        });
    }
};
