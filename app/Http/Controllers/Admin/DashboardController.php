<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalOrders = Order::query()->count();
        $totalProducts = Product::query()->count();

        $totalRevenue = Order::query()
            ->whereHas('payment', fn ($q) => $q->where('status', 'paid'))
            ->sum('grand_total_amount');

        $days = collect(range(0, 13))
            ->map(fn ($i) => Carbon::today()->subDays(13 - $i));

        $salesSeries = $days->map(function (Carbon $day) {
            $start = $day->copy()->startOfDay();
            $end = $day->copy()->endOfDay();

            $revenue = Order::query()
                ->whereBetween('created_at', [$start, $end])
                ->whereHas('payment', fn ($q) => $q->where('status', 'paid'))
                ->sum('grand_total_amount');

            return [
                'date' => $day->format('Y-m-d'),
                'revenue' => (int) $revenue,
            ];
        });

        return view('admin.dashboard.index', [
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalRevenue' => (int) $totalRevenue,
            'salesSeries' => $salesSeries,
        ]);
    }
}

