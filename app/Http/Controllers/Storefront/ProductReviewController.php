<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        ProductReview::query()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'],
            'status' => 'published',
        ]);

        return redirect()->to(route('products.show', [$product], false) . '#reviews')
            ->with('status', 'Terima kasih, ulasan Anda tersimpan.');
    }
}

