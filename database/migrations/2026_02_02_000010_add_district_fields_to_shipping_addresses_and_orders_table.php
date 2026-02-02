<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->string('district_id')->nullable()->after('city_id');
            $table->string('district_name')->nullable()->after('district_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_district_id')->nullable()->after('shipping_city_id');
            $table->string('shipping_district_name')->nullable()->after('shipping_district_id');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            $table->dropColumn(['district_id', 'district_name']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_district_id', 'shipping_district_name']);
        });
    }
};

