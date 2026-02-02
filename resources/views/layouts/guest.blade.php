<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CosplayerWardrobe') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-50 text-slate-900 antialiased">
        <div class="min-h-screen md:grid md:grid-cols-2">
            <!-- Left: Brand / Banner (desktop) -->
            <aside class="relative hidden overflow-hidden border-r border-slate-200 md:block">
                <div class="absolute inset-0">
                    <img
                        src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1800&q=80"
                        alt="Auth banner"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900/85 via-slate-900/55 to-slate-900/35"></div>
                </div>
                <div class="relative flex h-full flex-col justify-between p-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-white">
                        <span class="text-xl font-extrabold tracking-tight">CosplayerWardrobe</span>
                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">E-Commerce</span>
                    </a>

                    <div class="max-w-md text-white">
                        <p class="text-xs font-extrabold tracking-widest text-white/90">WELCOME</p>
                        <h1 class="mt-3 text-4xl font-extrabold tracking-tight">
                            Belanja makin cepat, tampilan makin modern.
                        </h1>
                        <p class="mt-4 text-sm leading-relaxed text-white/90">
                            Checkout cepat, pembayaran aman via Midtrans, dan ongkir otomatis via RajaOngkir.
                        </p>
                        <div class="mt-8 grid gap-3 text-sm text-white/95">
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2 w-2 rounded-full bg-white"></span>
                                UI clean & responsive
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2 w-2 rounded-full bg-white"></span>
                                Order & status terpantau
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2 w-2 rounded-full bg-white"></span>
                                Secure payment
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-white/80">© {{ date('Y') }} {{ config('app.name', 'CosplayerWardrobe') }}</p>
                </div>
            </aside>

            <!-- Right: Form -->
            <main class="flex items-center justify-center px-4 py-10 sm:px-6">
                <div class="w-full max-w-md">
                    <a href="{{ url('/') }}" class="mb-8 inline-flex items-center gap-2 md:hidden">
                        <span class="text-lg font-extrabold tracking-tight">
                            <span class="text-slate-900">Cosplayer</span><span class="text-slate-700">Wardrobe</span>
                        </span>
                    </a>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
