<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderController as StorefrontOrderController;
use App\Http\Controllers\Storefront\PaymentController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ProductReviewController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

// Root redirect ke storefront
Route::redirect('/', '/store', 302);

// Redirect URL lama (tanpa /store/) ke URL baru agar link/bookmark/email tetap jalan
Route::redirect('/orders', '/store/orders', 302);
Route::get('/orders/{id}', function (string $id) {
    return redirect('/store/orders/' . $id, 302);
})->whereNumber('id')->name('legacy.orders.show');
Route::redirect('/payments/finish', '/store/payments/finish', 302);
Route::get('/payments/{id}', function (string $id) {
    return redirect('/store/payments/' . $id, 302);
})->whereNumber('id');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('auth');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('auth');

// Storefront: semua URL di bawah /store/... (nama route tetap: home, products.index, dll)
Route::prefix('store')->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->name('cart.items.destroy');

    // Penting: definisikan finish SEBELUM /payments/{order} agar tidak ketabrak binding {order}
    Route::any('/payments/finish', [PaymentController::class, 'finish'])->name('payments.finish');

    Route::middleware('auth')->group(function () {
        Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        Route::get('/checkout/provinces', [CheckoutController::class, 'provinces'])->name('checkout.provinces');
        Route::get('/checkout/cities', [CheckoutController::class, 'cities'])->name('checkout.cities');
        Route::get('/checkout/districts', [CheckoutController::class, 'districts'])->name('checkout.districts');
        Route::post('/checkout/costs', [CheckoutController::class, 'costs'])->name('checkout.costs');

        Route::get('/orders', [StorefrontOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [StorefrontOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/confirm', [StorefrontOrderController::class, 'confirm'])->name('orders.confirm');
        Route::get('/orders/{order}/invoice', [StorefrontOrderController::class, 'invoice'])->name('orders.invoice');

        Route::get('/payments/{order}', [PaymentController::class, 'pay'])->name('payments.pay');
    });
    Route::post('/payments/midtrans/notification', [PaymentController::class, 'notification'])->name('payments.midtrans.notification');

    Route::view('/contact', 'storefront.contact')->name('contact');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';