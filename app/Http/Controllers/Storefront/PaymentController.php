<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request, Order $order)
    {
        // Izinkan jika: (1) pesanan milik user yang login, atau (2) redirect dari checkout dengan signed URL (session bisa bermasalah)
        $isOwner = (int) $order->user_id === (int) $request->user()->id;
        $hasValidSignedUrl = $request->hasValidSignature();

        if (!$isOwner && !$hasValidSignedUrl) {
            return redirect()->route('orders.index', [], false)
                ->with('error', 'Pesanan ini bukan milik akun Anda atau sesi berubah. Silakan cek riwayat pesanan di bawah.');
        }

        $order->load('payment');

        if (!$order->payment?->snap_token) {
            return redirect()->route('orders.show', [$order], false)
                ->withErrors(['payment' => 'Token pembayaran tidak tersedia. Silakan cek detail pesanan atau buat pesanan baru.']);
        }

        return view('storefront.payments.pay', [
            'order' => $order,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function finish(Request $request)
    {
        return view('storefront.payments.finish');
    }

    /**
     * Midtrans webhook endpoint (POST).
     */
    public function notification(Request $request, MidtransService $midtrans)
    {
        $midtrans->handleNotification($request->all());

        return response()->json(['ok' => true]);
    }
}

