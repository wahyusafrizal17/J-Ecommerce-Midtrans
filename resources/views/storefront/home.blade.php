<x-storefront-layout>
    <div class="grid gap-12">
        <!-- HERO BANNER (single image) -->
        <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white">
            <div class="absolute inset-0">
                <img
                    src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1800&q=80"
                    alt="Karma style banner"
                    class="h-full w-full object-cover"
                    loading="lazy"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-white/95 via-white/85 to-white/30"></div>

                <!-- Dark accents -->
                <div class="pointer-events-none absolute -right-24 -top-24 h-[520px] w-[520px] rotate-12 bg-slate-900/10"></div>
                <div class="pointer-events-none absolute -right-44 -top-44 h-[520px] w-[520px] rotate-12 bg-slate-900/15"></div>
                <div class="pointer-events-none absolute -right-64 -top-64 h-[520px] w-[520px] rotate-12 bg-slate-900/35"></div>
            </div>

            <div class="relative px-6 py-14 sm:px-10">
                <div class="grid items-center gap-10 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-extrabold tracking-widest text-slate-700">NEW COLLECTION</p>
                        <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                            Nike New Collection!
                        </h1>
                        <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-600">
                            Tampilan modern ala Karma: clean, bold, dan fokus ke produk. Jelajahi katalog, filter, lalu checkout cepat.
                        </p>

                        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-slate-800">
                                Shop Now
                            </a>
                            <a href="{{ route('products.index', ['sort' => 'recommended']) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-extrabold text-slate-900 hover:bg-slate-50">
                                Recommended
                            </a>
                        </div>
                    </div>

                    <div class="relative hidden md:block">
                        <div class="relative mx-auto aspect-[5/3] max-w-[520px]">
                            <div class="absolute inset-0 rounded-[40px] bg-white/50 backdrop-blur"></div>
                            <div class="absolute inset-0 grid place-items-center">
                                <div class="grid gap-2 text-center">
                                    <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-slate-900 text-white">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 6h15l-1.5 9h-12z" />
                                            <path d="M6 6l-2-3H2" />
                                            <circle cx="9" cy="21" r="1" />
                                            <circle cx="18" cy="21" r="1" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-extrabold text-slate-900">Weekly Deals</p>
                                    <p class="text-xs text-slate-600">Recommended • Popular</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES (like Karma) -->
        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $features = [
                    ['title' => 'Free Delivery', 'desc' => 'Gratis ongkir sesuai syarat & ketentuan'],
                    ['title' => 'Return Policy', 'desc' => 'Pengembalian mudah (sesuai ketentuan)'],
                    ['title' => '24/7 Support', 'desc' => 'Layanan pelanggan responsif'],
                    ['title' => 'Secure Payment', 'desc' => 'Pembayaran aman via Midtrans'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex items-start gap-3">
                            <div class="grid h-10 w-10 place-items-center rounded-2xl bg-slate-900/10 text-slate-900">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2l7 4v6c0 5-3 9-7 10-4-1-7-5-7-10V6z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-extrabold text-slate-900">{{ $f['title'] }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $f['desc'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="grid gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Kategori</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Lihat semua</a>
            </div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                @foreach($categories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-extrabold hover:border-slate-900/25 hover:bg-slate-900/5">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </section>

        <section class="grid gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Produk Unggulan</h2>
                <a href="{{ route('products.index', ['sort' => 'recommended']) }}" class="text-sm text-slate-600 hover:text-slate-900">Lihat semua</a>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($featuredProducts as $p)
                    <a href="{{ route('products.show', $p) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white hover:shadow-sm">
                        <div class="relative aspect-square bg-slate-100">
                            @if($p->primaryImage)
                                <img src="{{ $p->primaryImage->url() }}" alt="{{ $p->name }}" class="h-full w-full object-cover transition group-hover:scale-[1.02]">
                            @else
                                <div class="grid h-full w-full place-items-center bg-gradient-to-br from-slate-200 to-slate-100">
                                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white/80 text-slate-900">
                                        <span class="text-lg font-extrabold">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                    </div>
                                </div>
                            @endif
                            @if($p->is_recommended)
                                <div class="absolute left-3 top-3 rounded-full bg-slate-900 px-2.5 py-1 text-[10px] font-extrabold text-white">RECOMMENDED</div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-xs font-semibold text-slate-500">{{ $p->category->name ?? '-' }}</p>
                            <h3 class="mt-1 line-clamp-2 text-sm font-extrabold text-slate-900">{{ $p->name }}</h3>
                            <p class="mt-2 text-sm font-extrabold text-slate-900">{{ $p->displayPrice() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="grid gap-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Produk Terbaru</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Lihat semua</a>
            </div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($newArrivals as $p)
                    <a href="{{ route('products.show', $p) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white hover:shadow-sm">
                        <div class="relative aspect-square bg-slate-100">
                            @if($p->primaryImage)
                                <img src="{{ $p->primaryImage->url() }}" alt="{{ $p->name }}" class="h-full w-full object-cover transition group-hover:scale-[1.02]">
                            @else
                                <div class="grid h-full w-full place-items-center bg-gradient-to-br from-slate-200 to-slate-100">
                                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white/80 text-slate-900">
                                        <span class="text-lg font-extrabold">{{ strtoupper(mb_substr($p->name, 0, 1)) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-xs font-semibold text-slate-500">{{ $p->category->name ?? '-' }}</p>
                            <h3 class="mt-1 line-clamp-2 text-sm font-extrabold text-slate-900">{{ $p->name }}</h3>
                            <p class="mt-2 text-sm font-extrabold text-slate-900">{{ $p->displayPrice() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
</x-storefront-layout>

