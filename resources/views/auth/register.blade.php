<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold tracking-widest text-slate-700">CREATE ACCOUNT</p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Daftar</h1>
        <p class="mt-2 text-sm text-slate-600">Buat akun untuk mulai belanja dan simpan riwayat pesanan.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="grid gap-4">
        @csrf

        <div class="grid gap-1">
            <label for="name" class="text-sm font-semibold text-slate-800">Nama</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-900"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="grid gap-1">
            <label for="email" class="text-sm font-semibold text-slate-800">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="nama@email.com"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-900"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="grid gap-1">
            <label for="password" class="text-sm font-semibold text-slate-800">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-900"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="grid gap-1">
            <label for="password_confirmation" class="text-sm font-semibold text-slate-800">Konfirmasi Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi password"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-900"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="mt-2 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white hover:bg-slate-800">
            Register
        </button>

        <p class="mt-2 text-center text-sm text-slate-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-extrabold text-slate-900 hover:text-slate-700">Masuk</a>
        </p>
    </form>
</x-guest-layout>
