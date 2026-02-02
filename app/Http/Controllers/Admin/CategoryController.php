<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()->latest()->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();

        $slug = $this->uniqueSlug($data['name']);

        Category::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori dibuat.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $data = $request->validated();

        $category->fill([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        if ($category->isDirty('name')) {
            $category->slug = $this->uniqueSlug($category->name, $category->id);
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('status', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        $productsCount = $category->products()->count();
        if ($productsCount > 0) {
            return back()->withErrors([
                'category' => "Kategori sedang digunakan oleh {$productsCount} produk dan tidak bisa dihapus. Pindahkan/hapus produknya dulu.",
            ]);
        }

        try {
            $category->delete();
        } catch (QueryException $e) {
            // Fallback safety in case of any FK constraint / race condition.
            if ((string) $e->getCode() === '23000') {
                return back()->withErrors([
                    'category' => 'Kategori sedang digunakan dan tidak bisa dihapus.',
                ]);
            }

            throw $e;
        }

        return back()->with('status', 'Kategori dihapus.');
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            Category::query()
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

