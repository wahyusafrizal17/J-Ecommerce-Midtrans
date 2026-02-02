<x-admin-layout :header="'Edit Produk'">
    <div class="max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <h1 class="text-xl font-extrabold tracking-tight">Edit Produk</h1>
        <p class="mt-1 text-sm text-slate-600">{{ $product->name }}</p>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
            @csrf
            @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">Nama</label>
                    <input name="name" value="{{ old('name', $product->name) }}" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Kategori</label>
                    <select name="category_id" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (int) old('category_id', $product->category_id) === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Harga (Rupiah)</label>
                    <input name="price_amount" type="number" min="0" value="{{ old('price_amount', $product->price_amount) }}" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Stok</label>
                    <input name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                </div>
                <div class="flex items-end gap-6">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended', $product->is_recommended) ? 'checked' : '' }} class="rounded border-slate-300">
                        <span>Disarankan</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-slate-300">
                        <span>Aktif</span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">Deskripsi</label>
                    <textarea name="description" rows="5" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">Tambah Gambar (boleh multiple)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                </div>
            </div>

            <div class="flex gap-3">
                <button class="rounded-2xl bg-[#7367f0] px-5 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">Simpan</button>
                <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-200 px-5 py-2 text-sm font-semibold hover:bg-slate-50">Batal</a>
            </div>
        </form>

        @if($product->images->isNotEmpty())
            <div class="mt-6">
                <p class="text-xs font-semibold text-slate-600">Gambar saat ini</p>
                <div class="mt-2 grid grid-cols-5 gap-2">
                    @foreach($product->images as $img)
                        <div class="relative aspect-square overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                            <img src="{{ $img->url() }}" alt="" class="h-full w-full object-cover">

                            <form
                                action="{{ route('admin.products.images.destroy', [$product, $img]) }}"
                                method="POST"
                                class="absolute right-1 top-1"
                                onsubmit="return confirm('Hapus gambar ini?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-rose-600/90 px-2 py-1 text-[10px] font-extrabold text-white hover:bg-rose-700">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>

