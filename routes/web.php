<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderController as StorefrontOrderController;
use App\Http\Controllers\Storefront\PaymentController;
use App\Http\Controllers\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/checkout/provinces', [CheckoutController::class, 'provinces'])->name('checkout.provinces');
    Route::get('/checkout/cities', [CheckoutController::class, 'cities'])->name('checkout.cities');
    Route::get('/checkout/districts', [CheckoutController::class, 'districts'])->name('checkout.districts');
    Route::post('/checkout/costs', [CheckoutController::class, 'costs'])->name('checkout.costs');

    Route::get('/orders', [StorefrontOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [StorefrontOrderController::class, 'show'])->name('orders.show');

    Route::get('/payments/{order}', [PaymentController::class, 'pay'])->name('payments.pay');
});

Route::get('/payments/finish', [PaymentController::class, 'finish'])->name('payments.finish');
Route::post('/payments/midtrans/notification', [PaymentController::class, 'notification'])->name('payments.midtrans.notification');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';