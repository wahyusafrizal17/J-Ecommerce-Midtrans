<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CheckoutStoreRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Services\MidtransService;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request, RajaOngkirService $rajaOngkir)
    {
        $cart = $this->getUserCart($request)->load(['items.product']);

        abort_if($cart->items->isEmpty(), 404);

        $provinces = $rajaOngkir->getProvinces();
        $rajaOngkirReady = filled(config('rajaongkir.api_key')) && filled(config('rajaongkir.origin_district_id'));

        return view('storefront.checkout.index', [
            'cart' => $cart,
            'provinces' => $provinces,
            'defaultCourier' => config('rajaongkir.default_courier'),
            'rajaOngkirReady' => $rajaOngkirReady,
        ]);
    }

    public function provinces(RajaOngkirService $rajaOngkir)
    {
        return response()->json($rajaOngkir->getProvinces());
    }

    public function cities(Request $request, RajaOngkirService $rajaOngkir)
    {
        $data = $request->validate([
            'province_id' => ['required', 'string'],
        ]);

        return response()->json($rajaOngkir->getCities($data['province_id']));
    }

    public function districts(Request $request, RajaOngkirService $rajaOngkir)
    {
        $data = $request->validate([
            'city_id' => ['required', 'string'],
        ]);

        return response()->json($rajaOngkir->getDistricts($data['city_id']));
    }

    public function costs(Request $request, RajaOngkirService $rajaOngkir)
    {
        $data = $request->validate([
            'district_id' => ['required', 'string'],
            'courier' => ['nullable', 'string'],
            'weight_grams' => ['nullable', 'integer', 'min:1', 'max:30000'],
        ]);

        $weight = (int) ($data['weight_grams'] ?? config('rajaongkir.default_weight_grams'));

        return response()->json($rajaOngkir->getCosts($data['district_id'], $weight, $data['courier'] ?? null));
    }

    public function store(CheckoutStoreRequest $request, RajaOngkirService $rajaOngkir, MidtransService $midtrans)
    {
        $data = $request->validated();

        $user = $request->user();
        $cart = $this->getUserCart($request)->load(['items.product']);
        abort_if($cart->items->isEmpty(), 404);

        // Recalculate totals server-side
        $subtotal = (int) $cart->items->sum(fn ($i) => (int) $i->qty * (int) $i->product->price_amount);

        $weight = (int) config('rajaongkir.default_weight_grams');
        $services = $rajaOngkir->getCosts($data['district_id'], $weight, $data['courier']);
        $selected = collect($services)->firstWhere('service', $data['courier_service']);
        abort_if(!$selected, 422);

        $shippingAmount = (int) $selected['cost'];
        $grandTotal = $subtotal + $shippingAmount;

        try {
            return DB::transaction(function () use ($user, $cart, $data, $subtotal, $shippingAmount, $grandTotal, $selected, $midtrans) {
                // Save address book record (optional; also used as reference)
                $address = ShippingAddress::query()->create([
                    'user_id' => $user->id,
                    'label' => 'Checkout ' . now()->format('Y-m-d H:i'),
                    'recipient_name' => $data['recipient_name'],
                    'phone' => $data['phone'],
                    'address_line' => $data['address_line'],
                    'province_id' => $data['province_id'],
                    'province_name' => $data['province_name'],
                    'city_id' => $data['city_id'],
                    'city_name' => $data['city_name'],
                    'district_id' => $data['district_id'],
                    'district_name' => $data['district_name'],
                    'postal_code' => $data['postal_code'] ?? null,
                    'notes' => $data['customer_note'] ?? null,
                ]);

                $order = Order::query()->create([
                    'user_id' => $user->id,
                    'shipping_address_id' => $address->id,
                    'status' => 'pending',
                    'subtotal_amount' => $subtotal,
                    'shipping_amount' => $shippingAmount,
                    'grand_total_amount' => $grandTotal,
                    'shipping_recipient_name' => $data['recipient_name'],
                    'shipping_phone' => $data['phone'],
                    'shipping_address_line' => $data['address_line'],
                    'shipping_province_id' => $data['province_id'],
                    'shipping_province_name' => $data['province_name'],
                    'shipping_city_id' => $data['city_id'],
                    'shipping_city_name' => $data['city_name'],
                    'shipping_district_id' => $data['district_id'],
                    'shipping_district_name' => $data['district_name'],
                    'shipping_postal_code' => $data['postal_code'] ?? null,
                    'courier' => $data['courier'],
                    'courier_service' => $data['courier_service'],
                    'courier_etd' => $selected['etd'] ?? null,
                    'customer_note' => $data['customer_note'] ?? null,
                ]);

                foreach ($cart->items as $cartItem) {
                    /** @var Product $product */
                    $product = $cartItem->product->fresh();
                    if (!$product || !$product->is_active) {
                        abort(422, 'Produk tidak valid.');
                    }
                    if ($product->stock < $cartItem->qty) {
                        abort(422, 'Stok tidak cukup.');
                    }

                    $lineTotal = (int) $cartItem->qty * (int) $product->price_amount;

                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price_amount' => (int) $product->price_amount,
                        'qty' => (int) $cartItem->qty,
                        'line_total_amount' => $lineTotal,
                    ]);

                    // reserve stock (simple approach)
                    $product->decrement('stock', (int) $cartItem->qty);
                }

                $payment = Payment::query()->create([
                    'order_id' => $order->id,
                    'provider' => 'midtrans',
                    'status' => 'pending',
                    'amount' => $order->grand_total_amount,
                    'midtrans_order_id' => $order->order_number,
                ]);

                $order->load(['items', 'user']);
                $snap = $midtrans->createSnapTransaction($order);

                $payment->snap_token = $snap['token'];
                $payment->snap_redirect_url = $snap['redirect_url'];
                $payment->save();

                // clear cart
                $cart->items()->delete();

                // Use relative URL so redirect works even if APP_URL wrongly includes /public (e.g. shared hosting)
                return redirect()->to(route('payments.pay', [$order], false));
            });
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['checkout' => $e->getMessage()]);
        }
    }

    protected function getUserCart(Request $request): Cart
    {
        $userId = $request->user()->id;

        return Cart::query()->firstOrCreate(['user_id' => $userId], ['session_id' => null]);
    }
}

