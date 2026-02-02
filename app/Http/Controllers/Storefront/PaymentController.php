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
        abort_unless($order->user_id === $request->user()->id, 404);
        $order->load('payment');

        abort_if(!$order->payment?->snap_token, 404);

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

