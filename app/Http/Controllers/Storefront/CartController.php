<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->getCart($request)->load(['items.product.primaryImage']);

        return view('storefront.cart.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()->active()->findOrFail($data['product_id']);
        $qty = (int) ($data['qty'] ?? 1);

        if ($product->stock < $qty) {
            return back()->withErrors(['qty' => 'Stok tidak cukup.']);
        }

        $cart = $this->getCart($request);

        /** @var CartItem $item */
        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $newQty = ($item->exists ? $item->qty : 0) + $qty;
        if ($product->stock < $newQty) {
            return back()->withErrors(['qty' => 'Stok tidak cukup.']);
        }

        $item->qty = $newQty;
        $item->save();

        return redirect()->route('cart.index', [], false)->with('status', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $this->getCart($request);
        if ((int) $cartItem->cart_id !== (int) $cart->id) {
            return redirect()->route('cart.index', [], false)->with('error', 'Item tidak ditemukan.');
        }

        $cartItem->load('product');
        if ($cartItem->product && $cartItem->product->stock < (int) $data['qty']) {
            return redirect()->route('cart.index', [], false)->withErrors(['qty' => 'Stok tidak cukup.']);
        }

        $cartItem->qty = (int) $data['qty'];
        $cartItem->save();

        return redirect()->route('cart.index', [], false)->with('status', 'Keranjang diperbarui.');
    }

    public function destroy(Request $request, int|string $cartItem)
    {
        $cart = $this->getCart($request);
        $id = (int) $cartItem;

        $item = CartItem::query()
            ->where('id', $id)
            ->where('cart_id', $cart->id)
            ->first();

        if (!$item) {
            return redirect()->route('cart.index', [], false)
                ->with('error', 'Item tidak ditemukan atau sudah dihapus.');
        }

        $item->delete();

        return redirect()->route('cart.index', [], false)->with('status', 'Item dihapus.');
    }

    protected function getCart(Request $request): Cart
    {
        $userId = $request->user()?->id;
        $sessionId = $request->session()->getId();

        if ($userId) {
            return Cart::query()->firstOrCreate(
                ['user_id' => $userId],
                ['session_id' => null]
            );
        }

        return Cart::query()->firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null],
            []
        );
    }
}

