<?php

return [
    'api_key' => env('RAJAONGKIR_API_KEY'),
    // RajaOngkir API (Komerce) V2
    // Base URL docs: https://rajaongkir.komerce.id/api/v1/
    'base_url' => env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1'),

    /**
     * Origin ID untuk kalkulasi ongkir.
     * RajaOngkir API V2 (Komerce) menggunakan origin/destination berbasis DISTRICT (kecamatan).
     * Ambil nilainya dari endpoint: destination/district/{city_id}
     */
    'origin_district_id' => env('RAJAONGKIR_ORIGIN_DISTRICT_ID'),

    // Courier default untuk checkout
    'default_courier' => env('RAJAONGKIR_DEFAULT_COURIER', 'jne'),

    // Default weight if product has no weight implementation (grams)
    'default_weight_grams' => (int) env('RAJAONGKIR_DEFAULT_WEIGHT_GRAMS', 1000),
];

