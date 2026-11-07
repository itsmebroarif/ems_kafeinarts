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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->text('address')->nullable()->after('avatar');
            $table->string('instagram')->nullable()->after('address');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('linkedin')->nullable()->after('twitter');
            $table->string('github')->nullable()->after('linkedin');
            $table->string('website')->nullable()->after('github');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'avatar', 'address', 'instagram', 'twitter', 'linkedin', 'github', 'website']);
        });
    }
};
