<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
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
            return redirect()->to(route('orders.index', [], false))
                ->with('error', 'Pesanan ini bukan milik akun Anda atau sesi berubah. Silakan cek riwayat pesanan di bawah.');
        }

        $order->load('payment');

        if (!$order->payment?->snap_token) {
            return redirect()->to(route('orders.show', [$order], false))
                ->withErrors(['payment' => 'Token pembayaran tidak tersedia. Silakan cek detail pesanan atau buat pesanan baru.']);
        }

        return view('storefront.payments.pay', [
            'order' => $order,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function finish(Request $request, MidtransService $midtrans)
    {
        // Fallback: jika webhook tidak jalan (mis. di localhost), update status berdasar query dari Snap
        $orderNumber = (string) $request->query('order_id', '');
        $statusCode = (string) $request->query('status_code', '');
        $transactionStatus = (string) $request->query('transaction_status', '');

        if ($orderNumber !== '' && $statusCode === '200' && $transactionStatus !== '') {
            /** @var Order|null $order */
            $order = Order::query()->where('order_number', $orderNumber)->first();

            if ($order) {
                $mapped = $midtrans->mapPaymentStatus($transactionStatus, (string) $request->query('fraud_status'));

                if ($mapped === 'paid') {
                    $payment = Payment::query()->firstOrNew(['order_id' => $order->id]);
                    $payment->provider = 'midtrans';
                    $payment->status = 'paid';
                    $payment->amount = (int) $order->grand_total_amount;
                    $payment->midtrans_order_id = $orderNumber;
                    if (blank($payment->paid_at)) {
                        $payment->paid_at = now();
                    }
                    $payment->save();

                    if ($order->status === 'pending') {
                        $order->status = 'diproses';
                        $order->save();
                    }
                }
            }
        }

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

