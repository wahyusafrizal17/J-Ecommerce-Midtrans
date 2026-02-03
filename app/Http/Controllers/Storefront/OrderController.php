<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with('payment')
            ->latest()
            ->paginate(10);

        return view('storefront.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        // Order ada tapi bukan milik user yang login → 403 + pesan jelas (bukan 404)
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Pesanan ini bukan milik akun Anda.');
        }

        $order->load(['items', 'payment']);

        return view('storefront.orders.show', compact('order'));
    }
}

