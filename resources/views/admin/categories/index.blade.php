<x-admin-layout :header="'Kategori'">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight">Kategori</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola kategori produk.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="rounded-2xl bg-[#7367f0] px-4 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">Tambah</a>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Slug</th>
                <th class="px-4 py-3 text-left">Aktif</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach($categories as $cat)
                <tr>
                    <td class="px-4 py-3 font-semibold">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $cat->slug }}</td>
                    <td class="px-4 py-3">
                        <span class="rounded-full {{ $cat->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }} px-2 py-1 text-xs font-semibold">
                            {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold hover:bg-slate-50">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori?')">
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
        {{ $categories->links() }}
    </div>
</x-admin-layout>

