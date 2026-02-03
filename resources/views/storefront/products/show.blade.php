<x-storefront-layout>
    <div class="grid gap-8 lg:grid-cols-12">
        <section class="lg:col-span-7">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                <div class="aspect-square bg-slate-100">
                    @php
                        $hero = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    @endphp
                    @if($hero)
                        <img src="{{ $hero->url() }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                    @endif
                </div>

                @if($product->images->count() > 1)
                    <div class="grid grid-cols-6 gap-2 border-t border-slate-200 p-4">
                        @foreach($product->images as $img)
                            <div class="aspect-square overflow-hidden rounded-xl bg-slate-100">
                                <img src="{{ $img->url() }}" alt="" class="h-full w-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <aside class="lg:col-span-5">
            <div class="rounded-3xl border border-slate-200 bg-white p-6">
                <p class="text-sm text-slate-600">{{ $product->category->name ?? '-' }}</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">{{ $product->name }}</h1>
                <p class="mt-3 text-2xl font-bold">{{ $product->displayPrice() }}</p>

                <div class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                    <span>Stok:</span>
                    <span class="font-semibold text-slate-900">{{ $product->stock }}</span>
                    @if($product->is_recommended)
                        <span class="ml-2 rounded-full bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white">Rekomendasi</span>
                    @endif
                </div>

                <form action="{{ route('cart.store', [], false) }}" method="POST" class="mt-6 grid gap-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="grid grid-cols-3 gap-3">
                        <input name="qty" type="number" min="1" max="99" value="1" class="col-span-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                        <button class="col-span-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </form>

                @if($product->description)
                    <div class="mt-6 border-t border-slate-200 pt-6">
                        <h2 class="text-sm font-semibold">Deskripsi</h2>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $product->description }}</p>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-12">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Produk Terkait</h2>
                <a href="{{ route('products.index', ['category' => $product->category?->slug], false) }}" class="text-sm text-slate-600 hover:text-slate-900">Lihat kategori</a>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($related as $p)
                    <a href="{{ route('products.show', [$p], false) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white hover:shadow-sm">
                        <div class="aspect-square bg-slate-100">
                            @if($p->primaryImage)
                                <img src="{{ $p->primaryImage->url() }}" alt="{{ $p->name }}" class="h-full w-full object-cover transition group-hover:scale-[1.02]">
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="line-clamp-2 text-xs font-semibold">{{ $p->name }}</h3>
                            <p class="mt-1 text-xs font-bold">{{ $p->displayPrice() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-storefront-layout>

