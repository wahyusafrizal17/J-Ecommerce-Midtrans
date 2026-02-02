<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('shipping_address_id')->nullable()->constrained('shipping_addresses')->nullOnDelete()->cascadeOnUpdate();

            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('pending')->index();

            $table->unsignedBigInteger('subtotal_amount');
            $table->unsignedBigInteger('shipping_amount');
            $table->unsignedBigInteger('grand_total_amount');

            // shipping snapshot
            $table->string('shipping_recipient_name');
            $table->string('shipping_phone');
            $table->text('shipping_address_line');
            $table->string('shipping_province_id');
            $table->string('shipping_province_name');
            $table->string('shipping_city_id');
            $table->string('shipping_city_name');
            $table->string('shipping_postal_code')->nullable();

            // RajaOngkir service snapshot
            $table->string('courier')->nullable(); // e.g. jne, tiki, pos
            $table->string('courier_service')->nullable(); // e.g. REG, YES
            $table->string('courier_etd')->nullable(); // estimated delivery time text

            $table->text('customer_note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

