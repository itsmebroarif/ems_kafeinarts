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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_committee_id')->constrained('event_committees')->cascadeOnDelete();
            $table->decimal('base_salary', 14, 2)->default(0);
            $table->decimal('allowance', 14, 2)->default(0); // Tunjangan
            $table->decimal('overtime', 14, 2)->default(0); // Lembur
            $table->decimal('deduction', 14, 2)->default(0); // Potongan
            $table->decimal('net_salary', 14, 2)->default(0); // base + allowance + overtime - deduction
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('status', 16)->default('draft'); // draft | pending | paid
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
