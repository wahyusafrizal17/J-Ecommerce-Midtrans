<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin - ' . config('app.name', 'E-Commerce') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
@php
    $nav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'home'],
        ['label' => 'Produk', 'route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'box'],
        ['label' => 'Kategori', 'route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'tag'],
        ['label' => 'Pesanan', 'route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'icon' => 'receipt'],
        ['label' => 'Pengguna', 'route' => 'admin.users.index', 'match' => 'admin.users.*', 'icon' => 'users'],
        ['label' => 'Laporan', 'route' => 'admin.reports.sales', 'match' => 'admin.reports.*', 'icon' => 'chart'],
    ];

    $isActive = function (string $match) {
        return request()->routeIs($match);
    };

    $icon = function (string $name) {
        // Lightweight inline icons (Vuexy-like). Keep it minimal and consistent.
        return match ($name) {
            'home' => '<path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5Z"/>',
            'box' => '<path d="M21 8.5 12 3 3 8.5l9 5 9-5Z"/><path d="M3 8.5V20l9 5 9-5V8.5"/><path d="M12 13.5V21"/>',
            'tag' => '<path d="M20 10V4H14L4 14l6 6 10-10Z"/><path d="M16 8h.01"/>',
            'receipt' => '<path d="M6 2h12v20l-2-1-2 1-2-1-2 1-2-1-2 1V2Z"/><path d="M9 7h6"/><path d="M9 11h6"/><path d="M9 15h6"/>',
            'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'chart' => '<path d="M4 19V5"/><path d="M8 19V11"/><path d="M12 19V9"/><path d="M16 19V13"/><path d="M20 19V7"/>',
            default => '<path d="M12 20h.01"/>',
        };
    };
@endphp

<div
    x-data="{
        mobileOpen: false,
        // Keep it clean: default expanded sidebar (no icon-only bar)
        collapsed: false,
    }"
    class="min-h-screen"
>
    <div class="flex">
        <!-- Sidebar (desktop) -->
        <aside class="hidden w-[280px] border-r border-slate-200 bg-white md:sticky md:top-0 md:flex md:h-screen md:flex-col">
            <div class="px-5 py-5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-2xl bg-[#7367f0] text-white shadow-sm">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                            <path d="M12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5L12 3Z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-extrabold tracking-tight text-slate-900">Admin Panel</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ config('app.name', 'E-Commerce') }}</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 pb-6 text-sm">
                <p class="px-3 pb-2 text-[11px] font-extrabold tracking-widest text-slate-400">MENU</p>
                @foreach($nav as $item)
                    @php $active = $isActive($item['match']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="group mt-1 flex items-center gap-3 rounded-2xl px-3 py-2.5 transition"
                        @class([
                            'bg-[#7367f0]/10 text-[#7367f0]' => $active,
                            'text-slate-700 hover:bg-slate-50 hover:text-slate-900' => !$active,
                        ])
                    >
                        <span @class([
                            'h-6 w-1.5 rounded-full transition',
                            'bg-[#7367f0] opacity-100' => $active,
                            'bg-transparent opacity-0' => !$active,
                        ])></span>
                        <span class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-white text-slate-700 group-hover:bg-slate-50"
                              @class([$active ? 'border-[#7367f0]/20 bg-[#7367f0]/10 text-[#7367f0]' : ''])>
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                {!! $icon($item['icon']) !!}
                            </svg>
                        </span>
                        <span class="truncate font-semibold">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-slate-200 px-5 py-4 text-xs text-slate-500">
                <p class="font-semibold text-slate-700">Admin</p>
                <p class="mt-1">© {{ date('Y') }} {{ config('app.name', 'E-Commerce') }}</p>
            </div>
        </aside>

        <!-- Main -->
        <div class="min-w-0 flex-1">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/85 backdrop-blur">
                <div class="mx-auto flex max-w-[1400px] items-center justify-between gap-3 px-4 py-3 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button type="button" class="inline-flex rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50 md:hidden" @click="mobileOpen=true">
                            Menu
                        </button>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-extrabold text-slate-900">{{ $header ?? 'Dashboard' }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">Signed in as {{ auth()->user()->name }}</p>
                        </div>
                    </div>

                    <div class="hidden flex-1 px-4 lg:block">
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Search (UI only)..."
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 pr-10 text-sm outline-none focus:border-[#7367f0]"
                            />
                            <svg class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="7" />
                                <path d="M21 21l-4.3-4.3" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" class="hidden rounded-2xl border border-slate-200 bg-white p-2.5 text-slate-700 hover:bg-slate-50 sm:inline-flex" title="Notifications">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                        </button>

                        <a href="{{ route('home') }}" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50">
                            Storefront
                        </a>

                        <div x-data="{open:false}" class="relative">
                            <button @click="open=!open" type="button" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-extrabold hover:bg-slate-50">
                                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                                <span class="grid h-8 w-8 place-items-center rounded-xl bg-[#7367f0]/10 text-[#7367f0]">
                                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <svg class="h-4 w-4 text-slate-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-cloak x-show="open" @click.outside="open=false" class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm hover:bg-slate-50">Profil</a>
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-200">
                                    @csrf
                                    <button class="w-full px-4 py-3 text-left text-sm hover:bg-slate-50">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[1400px] px-4 py-6 sm:px-6">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile sidebar -->
    <div x-cloak x-show="mobileOpen" class="fixed inset-0 z-50 md:hidden">
        <div class="absolute inset-0 bg-black/50" @click="mobileOpen=false"></div>
        <div class="absolute inset-y-0 left-0 w-[300px] bg-white p-4 shadow-2xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-slate-900">
                    <div class="grid h-10 w-10 place-items-center rounded-2xl bg-[#7367f0] text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                            <path d="M12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5L12 3Z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-extrabold tracking-tight">Admin Panel</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ config('app.name', 'E-Commerce') }}</p>
                    </div>
                </a>
                <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold hover:bg-slate-50" @click="mobileOpen=false">
                    Tutup
                </button>
            </div>

            <nav class="mt-5 grid gap-1.5 text-sm">
                @foreach($nav as $item)
                    @php $active = $isActive($item['match']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 transition"
                        @class([
                            'bg-[#7367f0]/10 text-[#7367f0]' => $active,
                            'text-slate-700 hover:bg-slate-50 hover:text-slate-900' => !$active,
                        ])
                    >
                        <span @class([
                            'h-6 w-1.5 rounded-full transition',
                            'bg-[#7367f0] opacity-100' => $active,
                            'bg-transparent opacity-0' => !$active,
                        ])></span>
                        <span class="grid h-9 w-9 place-items-center rounded-xl"
                              @class([$active ? 'bg-[#7367f0]/10 text-[#7367f0] border border-[#7367f0]/20' : 'border border-slate-200 bg-white text-slate-700 group-hover:bg-slate-50'])>
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2">
                                {!! $icon($item['icon']) !!}
                            </svg>
                        </span>
                        <span class="truncate font-semibold">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
    </div>
</div>
</body>
</html>

