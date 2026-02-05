<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            // Hapus unique constraint lama (satu ulasan per user per produk)
            $table->dropUnique('product_reviews_product_id_user_id_unique');
            // Tambah index biasa untuk query
            $table->index(['product_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'user_id']);
            $table->unique(['product_id', 'user_id']);
        });
    }
};

