<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(8)
            ->get();

        $featuredProducts = Product::query()
            ->active()
            ->recommended()
            ->with(['category', 'primaryImage'])
            ->orderByDesc('sold_count')
            ->limit(8)
            ->get();

        $newArrivals = Product::query()
            ->active()
            ->with(['category', 'primaryImage'])
            ->latest()
            ->limit(8)
            ->get();

        return view('storefront.home', compact('categories', 'featuredProducts', 'newArrivals'));
    }
}

