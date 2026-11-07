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
            $table->string('midtrans_order_id')->nullable()->after('exchange_id');
            $table->string('midtrans_snap_token')->nullable()->after('midtrans_order_id');
            $table->text('midtrans_snap_redirect_url')->nullable()->after('midtrans_snap_token');
            $table->string('midtrans_status')->nullable()->after('midtrans_snap_redirect_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrants', function (Blueprint $table) {
            $table->dropColumn(['midtrans_order_id', 'midtrans_snap_token', 'midtrans_snap_redirect_url', 'midtrans_status']);
        });
    }
};
