<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected function client()
    {
        $baseUrl = rtrim((string) config('rajaongkir.base_url'), '/');

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->withHeaders([
                'key' => (string) config('rajaongkir.api_key'),
            ]);
    }

    protected function isConfigured(): bool
    {
        return filled(config('rajaongkir.api_key'));
    }

    /**
     * Komerce/RajaOngkir V2 biasanya mengembalikan `data`.
     * Untuk backward-compat, tetap coba baca `rajaongkir.results`.
     *
     * @return array<int, mixed>
     */
    protected function extractList(Response $response): array
    {
        $json = $response->json();

        $data = Arr::get($json, 'data', null);
        if (is_array($data)) {
            return $data;
        }

        $results = Arr::get($json, 'rajaongkir.results', []);
        return is_array($results) ? $results : [];
    }

    /**
     * @return array<int, array{id:string,name:string}>
     */
    protected function mapIdName(array $items): array
    {
        return collect($items)->map(fn ($row) => [
            'id' => (string) (Arr::get($row, 'id') ?? Arr::get($row, 'province_id') ?? Arr::get($row, 'city_id') ?? Arr::get($row, 'district_id')),
            'name' => (string) (Arr::get($row, 'name') ?? Arr::get($row, 'province') ?? Arr::get($row, 'city_name') ?? Arr::get($row, 'district')),
        ])->filter(fn ($r) => filled($r['id']) && filled($r['name']))->values()->all();
    }

    /**
     * @return array<int, array{id:string,name:string}>
     */
    public function getProvinces(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        return Cache::remember('rajaongkir:provinces', now()->addDays(7), function () {
            $res = $this->client()->get('/destination/province');
            if (!$res->successful()) {
                Log::warning('RajaOngkir getProvinces failed', ['status' => $res->status(), 'body' => $res->body()]);
                return [];
            }

            return $this->mapIdName($this->extractList($res));
        });
    }

    /**
     * @return array<int, array{id:string,name:string,type:string,postal_code:string|null}>
     */
    public function getCities(string $provinceId): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        return Cache::remember("rajaongkir:cities:{$provinceId}", now()->addDays(7), function () use ($provinceId) {
            $res = $this->client()->get("/destination/city/{$provinceId}");
            if (!$res->successful()) {
                Log::warning('RajaOngkir getCities failed', ['province_id' => $provinceId, 'status' => $res->status(), 'body' => $res->body()]);
                return [];
            }

            $items = $this->extractList($res);

            // Normalized shape for UI (keep `type` for display if provided)
            return collect($items)->map(fn ($c) => [
                'id' => (string) (Arr::get($c, 'id') ?? Arr::get($c, 'city_id')),
                'name' => (string) (Arr::get($c, 'name') ?? Arr::get($c, 'city_name')),
                'type' => (string) (Arr::get($c, 'type') ?? ''),
                'postal_code' => Arr::get($c, 'postal_code'),
            ])->filter(fn ($r) => filled($r['id']) && filled($r['name']))->values()->all();
        });
    }

    /**
     * @return array<int, array{id:string,name:string}>
     */
    public function getDistricts(string $cityId): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        return Cache::remember("rajaongkir:districts:{$cityId}", now()->addDays(30), function () use ($cityId) {
            $res = $this->client()->get("/destination/district/{$cityId}");
            if (!$res->successful()) {
                Log::warning('RajaOngkir getDistricts failed', ['city_id' => $cityId, 'status' => $res->status(), 'body' => $res->body()]);
                return [];
            }

            return $this->mapIdName($this->extractList($res));
        });
    }

    /**
     * @return array<int, array{service:string,description:string,cost:int,etd:string|null,note:string|null}>
     */
    public function getCosts(string $destinationDistrictId, int $weightGrams, ?string $courier = null): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $origin = (string) config('rajaongkir.origin_district_id');
        if (blank($origin)) {
            Log::warning('RajaOngkir origin_district_id is not configured');
            return [];
        }

        $courier = $courier ?: (string) config('rajaongkir.default_courier');

        $payload = [
            'origin' => $origin,
            'destination' => $destinationDistrictId,
            'weight' => max(1, $weightGrams),
            'courier' => $courier,
            'price' => 'lowest',
        ];

        $res = $this->client()->asForm()->post('/calculate/district/domestic-cost', $payload);
        if (!$res->successful()) {
            Log::warning('RajaOngkir getCosts failed', ['status' => $res->status(), 'body' => $res->body(), 'payload' => $payload]);
            return [];
        }

        $json = $res->json();

        // V2 responses can be:
        // 1) Flat list: data -> [ {service, description, cost, etd, ...}, ... ]
        // 2) Grouped list: data -> [ {courier, costs/services: [...]}, ... ]
        $rows = Arr::get($json, 'data', []);
        if (!is_array($rows)) {
            $rows = [];
        }

        // Case 1: Flat list of services
        if (!empty($rows) && is_array($rows[0]) && array_key_exists('service', $rows[0]) && array_key_exists('cost', $rows[0])) {
            return collect($rows)->map(function ($s) {
                return [
                    'service' => (string) Arr::get($s, 'service'),
                    'description' => (string) (Arr::get($s, 'description') ?? Arr::get($s, 'name') ?? ''),
                    'cost' => (int) Arr::get($s, 'cost', 0),
                    'etd' => Arr::get($s, 'etd') ? (string) Arr::get($s, 'etd') : null,
                    'note' => Arr::get($s, 'note') ? (string) Arr::get($s, 'note') : null,
                ];
            })->filter(fn ($s) => filled($s['service']) && (int) $s['cost'] > 0)->values()->all();
        }

        $services = collect();

        foreach ($rows as $row) {
            $costs = Arr::get($row, 'costs', Arr::get($row, 'services', []));
            if (!is_array($costs)) {
                continue;
            }

            foreach ($costs as $s) {
                // cost can be nested or flat depending on provider response
                $cost = Arr::get($s, 'cost.value', Arr::get($s, 'cost', Arr::get($s, 'price', 0)));
                $etd = Arr::get($s, 'cost.etd', Arr::get($s, 'etd'));
                $note = Arr::get($s, 'cost.note', Arr::get($s, 'note'));

                $services->push([
                    'service' => (string) (Arr::get($s, 'service') ?? Arr::get($s, 'service_code') ?? Arr::get($s, 'code')),
                    'description' => (string) (Arr::get($s, 'description') ?? Arr::get($s, 'service_name') ?? ''),
                    'cost' => (int) $cost,
                    'etd' => $etd ? (string) $etd : null,
                    'note' => $note ? (string) $note : null,
                ]);
            }
        }

        return $services
            ->filter(fn ($s) => filled($s['service']) && (int) $s['cost'] > 0)
            ->values()
            ->all();
    }
}

