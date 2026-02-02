<x-admin-layout :header="'Tambah Produk'">
    <div class="max-w-3xl rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <h1 class="text-xl font-extrabold tracking-tight">Tambah Produk</h1>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">Nama</label>
                    <input name="name" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Kategori</label>
                    <select name="category_id" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Harga (Rupiah)</label>
                    <input name="price_amount" type="number" min="0" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">Stok</label>
                    <input name="stock" type="number" min="0" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                </div>
                <div class="flex items-end gap-6">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_recommended" value="1" class="rounded border-slate-300">
                        <span>Disarankan</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300">
                        <span>Aktif</span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">Deskripsi</label>
                    <textarea name="description" rows="5" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">Gambar (boleh multiple)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    <p class="mt-2 text-xs text-slate-500">Gambar pertama akan jadi primary jika belum ada.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button class="rounded-2xl bg-[#7367f0] px-5 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">Simpan</button>
                <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-200 px-5 py-2 text-sm font-semibold hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>

