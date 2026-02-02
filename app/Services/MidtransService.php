<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        // Optional but useful in some flows
        MidtransConfig::$clientKey = config('midtrans.client_key');
        MidtransConfig::$isProduction = (bool) config('midtrans.is_production');
        MidtransConfig::$isSanitized = (bool) config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = (bool) config('midtrans.is_3ds');
    }

    public function isConfigured(): bool
    {
        return filled(config('midtrans.server_key')) && filled(config('midtrans.client_key'));
    }

    /**
     * Create (or refresh) Snap token for an order.
     *
     * @return array{token:string,redirect_url:string}
     */
    public function createSnapTransaction(Order $order): array
    {
        if (!filled(config('midtrans.server_key'))) {
            throw new \RuntimeException('Midtrans server key belum diisi. Isi MIDTRANS_SERVER_KEY di .env');
        }
        if (!filled(config('midtrans.client_key'))) {
            throw new \RuntimeException('Midtrans client key belum diisi. Isi MIDTRANS_CLIENT_KEY di .env');
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->grand_total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->shipping_phone,
            ],
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id' => (string) $item->product_id,
                    'price' => (int) $item->price_amount,
                    'quantity' => (int) $item->qty,
                    'name' => $item->product_name,
                ];
            })->values()->all(),
            'callbacks' => [
                'finish' => route('payments.finish'),
            ],
        ];

        try {
            $result = Snap::createTransaction($params);
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap createTransaction failed', [
                'order_number' => $order->order_number,
                'message' => $e->getMessage(),
            ]);
            $msg = $e->getMessage();

            // Improve common misconfiguration message
            if (str_contains($msg, 'Access denied') || str_contains($msg, 'unauthorized')) {
                $isProd = (bool) config('midtrans.is_production');
                $prefix = substr((string) config('midtrans.server_key'), 0, 12);
                $hint = $isProd
                    ? 'Mode PRODUCTION aktif, pastikan kamu pakai Production Server Key/Client Key dari Midtrans.'
                    : 'Mode SANDBOX aktif, pastikan kamu pakai Sandbox Server Key/Client Key dari Midtrans.';

                throw new \RuntimeException(
                    "Midtrans 401 Unauthorized. {$hint} (server key prefix: {$prefix})"
                );
            }

            throw $e;
        }

        return [
            'token' => $result->token,
            'redirect_url' => $result->redirect_url,
        ];
    }

    /**
     * Verify Midtrans signature_key.
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = (string) Arr::get($payload, 'order_id');
        $statusCode = (string) Arr::get($payload, 'status_code');
        $grossAmount = (string) Arr::get($payload, 'gross_amount');
        $signatureKey = (string) Arr::get($payload, 'signature_key');

        $serverKey = (string) config('midtrans.server_key');
        if ($serverKey === '') {
            return false;
        }

        $raw = $orderId . $statusCode . $grossAmount . $serverKey;
        $expected = hash('sha512', $raw);

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Handle payment notification/webhook payload from Midtrans.
     */
    public function handleNotification(array $payload): void
    {
        if (!$this->verifySignature($payload)) {
            Log::warning('Midtrans signature mismatch', ['payload' => $payload]);
            abort(403, 'Invalid signature');
        }

        $orderNumber = (string) Arr::get($payload, 'order_id');
        /** @var Order $order */
        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();

        $transactionStatus = (string) Arr::get($payload, 'transaction_status');
        $fraudStatus = (string) Arr::get($payload, 'fraud_status');

        $mapped = $this->mapPaymentStatus($transactionStatus, $fraudStatus);

        $payment = Payment::query()->firstOrNew(['order_id' => $order->id]);
        $payment->fill([
            'provider' => 'midtrans',
            'status' => $mapped,
            'amount' => (int) $order->grand_total_amount,
            'midtrans_order_id' => $orderNumber,
            'transaction_id' => Arr::get($payload, 'transaction_id'),
            'payment_type' => Arr::get($payload, 'payment_type'),
            'fraud_status' => $fraudStatus,
            'transaction_status' => $transactionStatus,
            'raw_response' => $payload,
        ]);

        if ($mapped === 'paid' && blank($payment->paid_at)) {
            $payment->paid_at = Carbon::now();
        }

        $payment->save();

        // Default business rule:
        // When payment is paid, move order to "diproses" automatically.
        if ($mapped === 'paid' && $order->status === 'pending') {
            $order->status = 'diproses';
            $order->save();
        }
    }

    public function mapPaymentStatus(string $transactionStatus, ?string $fraudStatus = null): string
    {
        $transactionStatus = strtolower($transactionStatus);
        $fraudStatus = strtolower((string) $fraudStatus);

        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'accept' ? 'paid' : 'pending';
        }

        if (in_array($transactionStatus, ['settlement'], true)) {
            return 'paid';
        }

        if (in_array($transactionStatus, ['pending'], true)) {
            return 'pending';
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
            return 'failed';
        }

        return 'pending';
    }
}

