<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $categorySlug = $request->query('category');
        $sort = $request->query('sort'); // cheapest | recommended | best

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $productsQuery = Product::query()
            ->active()
            ->with(['category', 'primaryImage']);

        if ($q !== '') {
            $productsQuery->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($categorySlug) {
            $productsQuery->whereHas('category', function ($c) use ($categorySlug) {
                $c->where('slug', $categorySlug);
            });
        }

        if ($sort === 'cheapest') {
            $productsQuery->orderBy('price_amount');
        } elseif ($sort === 'recommended') {
            $productsQuery->orderByDesc('is_recommended')->orderByDesc('sold_count');
        } elseif ($sort === 'best') {
            $productsQuery->orderByDesc('sold_count');
        } else {
            $productsQuery->latest();
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        return view('storefront.products.index', compact('categories', 'products', 'q', 'categorySlug', 'sort'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'images', 'reviews.user']);

        $averageRating = $product->averageRating();
        $reviews = $product->reviews()->latest()->get();
        $canReview = false;

        if (auth()->check()) {
            $canReview = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($q) {
                    $q->where('user_id', auth()->id())
                      ->where('status', 'selesai');
                })
                ->exists();
        }

        $related = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with('primaryImage')
            ->orderByDesc('sold_count')
            ->limit(6)
            ->get();

        return view('storefront.products.show', compact('product', 'related', 'reviews', 'averageRating', 'canReview'));
    }
}

