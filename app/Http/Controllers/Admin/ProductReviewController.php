<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = ProductReview::query()
            ->with(['product', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, ProductReview $review)
    {
        $data = $request->validate([
            'seller_reply' => ['nullable', 'string', 'max:3000'],
            'status' => ['required', 'in:published,hidden'],
        ]);

        $review->seller_reply = $data['seller_reply'] ?: null;
        $review->status = $data['status'];
        $review->seller_replied_at = $review->seller_reply ? now() : null;
        $review->save();

        return redirect()->back()->with('status', 'Respon penjual berhasil disimpan.');
    }
}

