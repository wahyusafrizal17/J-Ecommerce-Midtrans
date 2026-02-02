<x-admin-layout :header="'Tambah Kategori'">
    <div class="max-w-2xl rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <h1 class="text-xl font-extrabold tracking-tight">Tambah Kategori</h1>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-6 grid gap-4">
            @csrf
            <div>
                <label class="text-xs font-semibold text-slate-600">Nama</label>
                <input name="name" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600">Deskripsi</label>
                <textarea name="description" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0"></textarea>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300">
                <span>Aktif</span>
            </label>
            <div class="flex gap-3">
                <button class="rounded-2xl bg-[#7367f0] px-5 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">Simpan</button>
                <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-slate-200 px-5 py-2 text-sm font-semibold hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>

