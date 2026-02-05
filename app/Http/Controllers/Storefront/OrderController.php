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
        $currentUserId = (int) $request->user()->id;
        $orderUserId = (int) $order->user_id;

        if ($orderUserId !== $currentUserId) {
            return redirect()->to(route('orders.index', [], false))
                ->with('error', 'Pesanan ini bukan milik akun Anda atau sesi berubah. Silakan cek riwayat pesanan di bawah.');
        }

        $order->load(['items.product', 'payment']);

        return view('storefront.orders.show', compact('order'));
    }

    public function confirm(Request $request, Order $order)
    {
        $currentUserId = (int) $request->user()->id;
        $orderUserId = (int) $order->user_id;

        if ($orderUserId !== $currentUserId) {
            return redirect()->to(route('orders.index', [], false))
                ->with('error', 'Pesanan ini bukan milik akun Anda.');
        }

        if (! in_array($order->status, ['diproses', 'dikirim'], true)) {
            return redirect()->to(route('orders.show', [$order], false))
                ->with('error', 'Pesanan hanya bisa dikonfirmasi diterima ketika statusnya diproses atau dikirim.');
        }

        $order->status = 'selesai';
        $order->save();

        return redirect()->to(route('orders.show', [$order], false))
            ->with('status', 'Terima kasih! Pesanan telah dikonfirmasi sudah diterima.');
    }
}

