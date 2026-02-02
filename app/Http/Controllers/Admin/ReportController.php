<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : now()->subDays(29)->startOfDay();
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : now()->endOfDay();

        $paidOrders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('payment', fn ($q) => $q->where('status', 'paid'))
            ->with(['user', 'payment'])
            ->latest()
            ->get();

        $totalOmzet = (int) $paidOrders->sum('grand_total_amount');

        // Daily aggregation (portable)
        $daily = $paidOrders
            ->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))
            ->map(fn ($group, $date) => [
                'date' => $date,
                'orders' => $group->count(),
                'revenue' => (int) $group->sum('grand_total_amount'),
            ])
            ->sortBy('date')
            ->values();

        return view('admin.reports.sales', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'paidOrders' => $paidOrders,
            'totalOmzet' => $totalOmzet,
            'daily' => $daily,
        ]);
    }

    public function exportSalesCsv(Request $request): StreamedResponse
    {
        $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : now()->subDays(29)->startOfDay();
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : now()->endOfDay();

        $orders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('payment', fn ($q) => $q->where('status', 'paid'))
            ->with('user')
            ->latest()
            ->cursor();

        $filename = 'sales-report-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['order_number', 'date', 'customer', 'email', 'status', 'subtotal', 'shipping', 'grand_total']);
            foreach ($orders as $o) {
                fputcsv($out, [
                    $o->order_number,
                    $o->created_at->format('Y-m-d H:i:s'),
                    $o->user?->name,
                    $o->user?->email,
                    $o->status,
                    $o->subtotal_amount,
                    $o->shipping_amount,
                    $o->grand_total_amount,
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

