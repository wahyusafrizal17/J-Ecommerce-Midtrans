<x-admin-layout :header="'Produk'">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight">Produk</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola katalog produk.</p>
        </div>
        <div class="flex gap-3">
            <form method="GET" class="flex items-center gap-2">
                <input name="q" value="{{ $q }}" placeholder="Cari produk..." class="w-64 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Cari</button>
            </form>
            <a href="{{ route('admin.products.create') }}" class="rounded-2xl bg-[#7367f0] px-4 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">Tambah</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">Produk</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Harga</th>
                <th class="px-4 py-3 text-left">Stok</th>
                <th class="px-4 py-3 text-left">Disarankan</th>
                <th class="px-4 py-3 text-left">Aktif</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach($products as $p)
                <tr>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 overflow-hidden rounded-xl bg-slate-100">
                                @if($p->primaryImage)
                                    <img src="{{ $p->primaryImage->url() }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{{ $p->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $p->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-700">{{ $p->category?->name }}</td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($p->price_amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $p->stock }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full {{ $p->is_recommended ? 'bg-[#7367f0] text-white' : 'bg-slate-100 text-slate-700' }} px-2 py-1 text-xs font-semibold">
                            {{ $p->is_recommended ? 'Ya' : 'Tidak' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="rounded-full {{ $p->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }} px-2 py-1 text-xs font-semibold">
                            {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $p) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold hover:bg-slate-50">Edit</a>
                            <form action="{{ route('admin.products.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus produk?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold hover:bg-slate-50">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $products->links() }}
    </div>
</x-admin-layout>

