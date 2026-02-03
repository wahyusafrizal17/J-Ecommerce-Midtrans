<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'E-Commerce') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900 antialiased">
@php
    $cartCount = 0;
    try {
        if (auth()->check()) {
            $cartCount = \App\Models\Cart::query()->where('user_id', auth()->id())->withCount('items')->value('items_count') ?? 0;
        } else {
            $cartCount = \App\Models\Cart::query()->where('session_id', session()->getId())->withCount('items')->value('items_count') ?? 0;
        }
    } catch (\Throwable $e) {
        $cartCount = 0;
    }
@endphp

<header class="sticky top-0 z-40 border-b border-slate-800 bg-slate-900/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('home', [], false) }}" class="text-lg font-extrabold tracking-tight">
                <span class="text-white">Cosplayer</span><span class="text-slate-200">Wardrobe</span>
            </a>
        </div>

        @php
            $navBase = 'text-white/85 hover:text-white';
            $navActive = 'text-white font-extrabold underline decoration-2 decoration-white/90 underline-offset-8';
        @endphp

        <nav class="hidden items-center gap-6 text-sm font-semibold md:flex">
            <a href="{{ route('home', [], false) }}" class="{{ request()->routeIs('home') ? $navActive : $navBase }}">Home</a>
            <a href="{{ route('products.index', [], false) }}" class="{{ request()->routeIs('products.*') ? $navActive : $navBase }}">Shop</a>
            @auth
                <a href="{{ route('orders.index', [], false) }}" class="{{ request()->routeIs('orders.*') ? $navActive : $navBase }}">Orders</a>
            @endauth
            <a href="{{ route('products.index', ['sort' => 'recommended'], false) }}" class="{{ request()->fullUrlIs(route('products.index', ['sort' => 'recommended'])) ? $navActive : $navBase }}">Deals</a>
        </nav>

        <div class="flex items-center gap-2">
            <form action="{{ route('products.index', [], false) }}" method="GET" class="hidden md:block">
                <div class="relative">
                    <input
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Search products..."
                        class="w-72 rounded-full border border-slate-700 bg-slate-800 px-4 py-2 pr-10 text-sm text-slate-100 outline-none placeholder:text-slate-400 focus:border-slate-500 focus:ring-0"
                    />
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 21l-4.3-4.3" />
                        <circle cx="11" cy="11" r="7" />
                    </svg>
                </div>
            </form>

            <a href="{{ route('cart.index', [], false) }}" class="relative inline-flex items-center rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                <svg class="mr-2 h-4 w-4 text-slate-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 6h15l-1.5 9h-12z" />
                    <path d="M6 6l-2-3H2" />
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="18" cy="21" r="1" />
                </svg>
                Cart
                @if($cartCount > 0)
                    <span class="ml-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-white px-1.5 text-xs font-extrabold text-slate-900">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                <div x-data="{open:false}" class="relative">
                    <button @click="open=!open" type="button" class="hidden items-center rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 md:inline-flex">
                        {{ auth()->user()->name }}
                        <svg class="ml-2 h-4 w-4 text-slate-200" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                        <a href="{{ route('orders.index', [], false) }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Riwayat Pesanan</a>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Profil</a>
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Admin Panel</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-200">
                            @csrf
                            <button class="w-full px-4 py-3 text-left text-sm hover:bg-slate-50">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login', [], false) }}" class="rounded-full bg-white px-4 py-2 text-sm font-extrabold text-slate-900 hover:bg-slate-100">Login</a>
            @endauth
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</main>

<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 text-sm text-slate-600 sm:px-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} {{ config('app.name', 'E-Commerce') }}. All rights reserved.</p>
            <p class="text-slate-500">Powered by Laravel 12 + Tailwind CSS.</p>
        </div>
    </div>
</footer>
</body>
</html>

