<x-storefront-layout>
    <div class="grid gap-6 lg:grid-cols-12">
        <aside class="lg:col-span-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-semibold">Filter</h2>

                <div class="mt-4">
                    <p class="text-xs font-semibold text-slate-500">Kategori</p>
                    <div class="mt-2 grid gap-1">
                        <a href="{{ route('products.index', array_filter(['q' => $q, 'sort' => $sort])) }}"
                           class="rounded-lg px-3 py-2 text-sm {{ !$categorySlug ? 'bg-slate-900 text-white' : 'hover:bg-slate-50' }}">
                            Semua
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('products.index', array_filter(['q' => $q, 'sort' => $sort, 'category' => $cat->slug])) }}"
                               class="rounded-lg px-3 py-2 text-sm {{ $categorySlug === $cat->slug ? 'bg-slate-900 text-white' : 'hover:bg-slate-50' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-xs font-semibold text-slate-500">Urutkan</p>
                    <div class="mt-2 grid gap-1">
                        @php
                            $base = array_filter(['q' => $q, 'category' => $categorySlug]);
                        @endphp
                        <a href="{{ route('products.index', $base) }}"
                           class="rounded-lg px-3 py-2 text-sm {{ !$sort ? 'bg-slate-900 text-white' : 'hover:bg-slate-50' }}">Terbaru</a>
                        <a href="{{ route('products.index', array_merge($base, ['sort' => 'cheapest'])) }}"
                           class="rounded-lg px-3 py-2 text-sm {{ $sort === 'cheapest' ? 'bg-slate-900 text-white' : 'hover:bg-slate-50' }}">Termurah</a>
                        <a href="{{ route('products.index', array_merge($base, ['sort' => 'recommended'])) }}"
                           class="rounded-lg px-3 py-2 text-sm {{ $sort === 'recommended' ? 'bg-slate-900 text-white' : 'hover:bg-slate-50' }}">Disarankan</a>
                        <a href="{{ route('products.index', array_merge($base, ['sort' => 'best'])) }}"
                           class="rounded-lg px-3 py-2 text-sm {{ $sort === 'best' ? 'bg-slate-900 text-white' : 'hover:bg-slate-50' }}">Best Seller</a>
                    </div>
                </div>
            </div>
        </aside>

        <section class="lg:col-span-9">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold">Produk</h1>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $products->total() }} item
                        @if($q) · Pencarian: <span class="font-semibold">{{ $q }}</span>@endif
                    </p>
                </div>
                <form action="{{ route('products.index') }}" method="GET" class="w-full max-w-sm">
                    <input type="hidden" name="category" value="{{ $categorySlug }}">
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input name="q" value="{{ $q }}" placeholder="Cari produk..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                </form>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @forelse($products as $p)
                    <a href="{{ route('products.show', $p) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white hover:shadow-sm">
                        <div class="aspect-square bg-slate-100">
                            @if($p->primaryImage)
                                <img src="{{ $p->primaryImage->url() }}" alt="{{ $p->name }}" class="h-full w-full object-cover transition group-hover:scale-[1.02]">
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-slate-500">{{ $p->category->name ?? '-' }}</p>
                            <h3 class="mt-1 line-clamp-2 text-sm font-semibold">{{ $p->name }}</h3>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-sm font-bold">{{ $p->displayPrice() }}</p>
                                @if($p->is_recommended)
                                    <span class="rounded-full bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white">Rekomendasi</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-600">
                        Produk tidak ditemukan.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </section>
    </div>
</x-storefront-layout>

