<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->text('venue_details')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('max_registrants')->default(0);
            $table->integer('timezone')->default(0);
            $table->integer('registrant_hours')->default(0);
            $table->integer('delete_at')->default(0);
            $table->integer('dudent_at')->default(0);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('fee', 12, 2)->default(0);
            $table->decimal('submit_fee', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('event_ticket_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2);
            $table->decimal('fee', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_downloadable')->default(true);
            $table->boolean('is_show_in_list')->default(true);
            $table->boolean('is_selectable_on_site')->default(true);
            $table->boolean('is_selectable_online')->default(true);
            $table->boolean('is_show_row_flag')->default(false);
            $table->string('row_flag_type')->nullable();
            $table->integer('seat_qty')->default(1);
            $table->integer('stock_qty')->default(0);
            $table->integer('sold_qty')->default(0);
            $table->boolean('is_metasuggest')->default(false);
            $table->integer('qty')->default(0);
            $table->timestamps();
        });

        Schema::create('field_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code');
            $table->string('type');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_hide_on_form')->default(false);
            $table->boolean('is_required_to_reg')->default(false);
            $table->boolean('is_required_to_payment')->default(false);
            $table->boolean('is_allow_much_more')->default(false);
            $table->integer('order_by')->default(0);
            $table->boolean('is_show_in_list')->default(false);
            $table->timestamps();
        });

        Schema::create('event_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();

            $table->string('code')->unique();
            $table->string('slug')->unique();
            $table->text('placeholder')->nullable();
            $table->string('default_value')->nullable();
            $table->string('validation')->nullable();
            $table->integer('order_by')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_hide_on_form')->default(false);
            $table->boolean('is_required_to_reg')->default(false);
            $table->boolean('is_required_to_payment')->default(false);
            $table->boolean('is_allow_much_more')->default(false);
            $table->boolean('is_show_in_list')->default(false);
            $table->timestamps();
        });

        Schema::create('event_addons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('fee', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_new')->default(false);
            $table->boolean('is_show_in_list')->default(true);
            $table->boolean('is_allow_buying_stack')->default(false);
            $table->integer('qty')->default(0);
            $table->integer('max_qty')->default(0);
            $table->integer('sold_qty')->default(0);
            $table->boolean('is_selectable_on_site')->default(true);
            $table->boolean('is_selectable_online')->default(true);
            $table->boolean('is_metasuggest')->default(false);
            $table->integer('buy_qty_step')->default(0);
            $table->integer('show_qty')->default(0);
            $table->boolean('is_displayed')->default(true);
            $table->boolean('is_displayed_on_backend')->default(true);
            $table->string('display_type')->default('count');
            $table->boolean('is_fit_disable')->default(false);
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });

        Schema::create('addon_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('addon_id')->constrained('event_addons')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('slug')->unique();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('fee', 12, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_highlight')->default(false);
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });

        Schema::create('event_vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->boolean('is_percent')->default(true);
            $table->decimal('value', 12, 2);
            $table->boolean('is_single_use')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('total_uses')->default(0);
            $table->integer('uses')->default(0);
            $table->boolean('is_auto_apply')->default(false);
            $table->boolean('is_show_in_list')->default(true);
            $table->boolean('is_show_on_front')->default(true);
            $table->boolean('is_show_on_payment')->default(true);
            $table->date('expired_at')->nullable();
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });

        Schema::create('registrants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index('user_id');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('nationality')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('guests_quantity')->nullable();
            $table->string('registrant_type')->nullable();
            $table->string('guests_quantity_other')->nullable();
            $table->foreignUuid('event_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('ticket_tier_id')->constrained('event_ticket_tiers')->cascadeOnDelete();
            $table->foreignUuid('voucher_id')->nullable()->constrained('event_vouchers')->nullOnDelete();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_rejected')->default(false);
            $table->boolean('is_pending')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_referred')->default(false);
            $table->boolean('is_hide')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->text('notes')->nullable();
            $table->text('custom_fields')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('addons_total', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('fee', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('shipping_method')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('paid_by')->nullable();
            $table->string('exchange_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('device_info')->nullable();
            $table->string('device_type')->nullable();
            $table->string('os_version')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('locale')->nullable();
            $table->boolean('is_wallet_payment')->default(false);
            $table->string('employee_id')->nullable();
            $table->boolean('is_remove')->default(false);
            $table->string('to_payment_url')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });

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

    public function down(): void
    {
        Schema::dropIfExists('registrant_addons');
        Schema::dropIfExists('ticket_items');
        Schema::dropIfExists('registrants');
        Schema::dropIfExists('event_vouchers');

        Schema::dropIfExists('event_addons');
        Schema::dropIfExists('event_custom_fields');
        Schema::dropIfExists('event_custom_field_types');
        Schema::dropIfExists('event_ticket_tiers');
        Schema::dropIfExists('events');
    }
};
