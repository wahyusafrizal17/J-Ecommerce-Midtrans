<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-extrabold tracking-widest text-slate-700">WELCOME BACK</p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Masuk</h1>
        <p class="mt-2 text-sm text-slate-600">Masuk untuk melanjutkan belanja dan cek status pesananmu.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-sm text-emerald-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="grid gap-4">
        @csrf

        <div class="grid gap-1">
            <label for="email" class="text-sm font-semibold text-slate-800">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
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
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-900"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900" name="remember">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-slate-700 hover:text-slate-900" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" class="mt-2 inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white hover:bg-slate-800">
            Log in
        </button>

        <p class="mt-2 text-center text-sm text-slate-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-extrabold text-slate-900 hover:text-slate-700">Daftar</a>
        </p>
    </form>
</x-guest-layout>
