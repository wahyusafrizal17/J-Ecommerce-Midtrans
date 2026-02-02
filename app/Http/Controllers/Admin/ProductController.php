<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'q'));
    }

    public function create()
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        $product = Product::query()->create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'price_amount' => (int) $data['price_amount'],
            'stock' => (int) $data['stock'],
            'is_recommended' => (bool) ($data['is_recommended'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $this->syncImages($product, $request);

        return redirect()->route('admin.products.index')->with('status', 'Produk dibuat.');
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $data = $request->validated();

        $product->fill([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price_amount' => (int) $data['price_amount'],
            'stock' => (int) $data['stock'],
            'is_recommended' => (bool) ($data['is_recommended'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        if ($product->isDirty('name')) {
            $product->slug = $this->uniqueSlug($product->name, $product->id);
        }

        $product->save();

        $this->syncImages($product, $request);

        return redirect()->route('admin.products.index')->with('status', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->load('images');

        foreach ($product->images as $img) {
            Storage::disk('public_uploads')->delete($img->path);
        }

        $product->delete();

        return back()->with('status', 'Produk dihapus.');
    }

    protected function syncImages(Product $product, Request $request): void
    {
        $files = $request->file('images', []);
        if (!$files || !is_array($files)) {
            return;
        }

        Storage::disk('public_uploads')->makeDirectory('products');

        $startSort = (int) ($product->images()->max('sort_order') ?? 0);

        foreach (array_values($files) as $idx => $file) {
            $path = Storage::disk('public_uploads')->putFile('products', $file);

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $path,
                'sort_order' => $startSort + $idx + 1,
                'is_primary' => $product->images()->where('is_primary', true)->doesntExist() && $idx === 0,
            ]);
        }

        // Ensure at least one primary image if any images exist
        if ($product->images()->exists() && $product->images()->where('is_primary', true)->doesntExist()) {
            $first = $product->images()->orderBy('sort_order')->first();
            if ($first) {
                $first->is_primary = true;
                $first->save();
            }
        }
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Product::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}

