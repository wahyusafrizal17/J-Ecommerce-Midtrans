<x-admin-layout :header="'Pengguna'">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight">Pengguna</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola role dan status akun.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input name="q" value="{{ $q }}" placeholder="Cari nama/email..." class="w-64 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
            <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Cari</button>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">User</th>
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">Aktif</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach($users as $u)
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $u->name }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $u->email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.users.update', $u) }}" method="POST" class="flex items-center justify-end gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="role" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                                <option value="customer" {{ $u->role === 'customer' ? 'selected' : '' }}>customer</option>
                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>admin</option>
                            </select>
                    </td>
                    <td class="px-4 py-3">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" name="is_active" value="1" {{ $u->is_active ? 'checked' : '' }} class="rounded border-slate-300">
                                <span class="text-slate-600">Aktif</span>
                            </label>
                    </td>
                    <td class="px-4 py-3 text-right">
                            <button class="rounded-2xl bg-[#7367f0] px-4 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">Simpan</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $users->links() }}
    </div>
</x-admin-layout>

