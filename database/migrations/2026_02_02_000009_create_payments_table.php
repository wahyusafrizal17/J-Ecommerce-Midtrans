<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('provider')->default('midtrans')->index();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending')->index();
            $table->unsignedBigInteger('amount');

            // Midtrans Snap fields
            $table->string('midtrans_order_id')->nullable()->index();
            $table->string('snap_token')->nullable();
            $table->string('snap_redirect_url')->nullable();

            // Midtrans transaction info
            $table->string('transaction_id')->nullable()->index();
            $table->string('payment_type')->nullable();
            $table->string('fraud_status')->nullable();
            $table->string('transaction_status')->nullable()->index();
            $table->timestamp('paid_at')->nullable()->index();

            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->unique(['order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

