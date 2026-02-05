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

                <div class="mt-3 flex items-center gap-2 text-sm">
                    @php
                        $avg = $averageRating;
                        $rounded = floor($avg);
                    @endphp
                    <div class="flex items-center gap-0.5 text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $rounded)
                                <svg class="h-4 w-4 fill-amber-400" viewBox="0 0 20 20"><path d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
                            @else
                                <svg class="h-4 w-4 fill-slate-200" viewBox="0 0 20 20"><path d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
                            @endif
                        @endfor
                    </div>
                    <span class="text-xs text-slate-600">
                        {{ $avg > 0 ? number_format($avg, 1, ',', '.') : 'Belum ada rating' }}
                        @if($reviews->count() > 0)
                            · {{ $reviews->count() }} ulasan
                        @endif
                    </span>
                </div>

                <p class="mt-3 text-2xl font-bold">{{ $product->displayPrice() }}</p>

                <div class="mt-3 flex items-center gap-2 text-sm">
                    <span class="text-slate-600">Stok:</span>
                    @if($product->stock <= 0)
                        <span class="font-semibold text-rose-600">Habis</span>
                    @else
                        <span class="font-semibold text-slate-900">{{ $product->stock }}</span>
                    @endif
                    @if($product->is_recommended)
                        <span class="ml-2 rounded-full bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white">Rekomendasi</span>
                    @endif
                </div>

                @if($product->stock > 0)
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
                @else
                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        Produk ini sedang <span class="font-semibold text-rose-600">stok habis</span>. Silakan cek kembali beberapa waktu lagi
                        atau hubungi kami melalui halaman <a href="{{ route('contact', [], false) }}" class="font-semibold text-slate-900 underline underline-offset-2">Contact Us</a>.
                    </div>
                @endif

                @if($product->description)
                    <div class="mt-6 border-t border-slate-200 pt-6">
                        <h2 class="text-sm font-semibold">Deskripsi</h2>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $product->description }}</p>
                    </div>
                @endif

                <div id="reviews" class="mt-6 border-t border-slate-200 pt-6">
                    <h2 class="text-sm font-semibold">Ulasan &amp; Rating</h2>

                    @auth
                        <div class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold text-slate-600 mb-2">
                                {{ $userReview ? 'Ubah ulasan Anda' : 'Tulis ulasan tentang produk ini' }}
                            </p>
                            <form action="{{ route('products.reviews.store', [$product], false) }}" method="POST" class="grid gap-3">
                                @csrf
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-slate-700">Rating:</span>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="flex items-center gap-1">
                                                <input type="radio" name="rating" value="{{ $i }}" class="h-4 w-4 text-amber-500 focus:ring-amber-500"
                                                       @checked(old('rating', $userReview->rating ?? 5) == $i)>
                                                <span class="text-xs text-slate-600">{{ $i }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <textarea
                                    name="comment"
                                    rows="3"
                                    class="w-full rounded-2xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0"
                                    placeholder="Ceritakan pengalamanmu dengan produk ini...">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                                <button class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                    Simpan ulasan
                                </button>
                            </form>
                        </div>
                    @else
                        <p class="mt-3 text-xs text-slate-600">
                            <a href="{{ route('login', [], false) }}" class="font-semibold text-slate-900 hover:underline">Masuk</a>
                            untuk menulis ulasan tentang produk ini.
                        </p>
                    @endauth

                    @if($reviews->isNotEmpty())
                        <div class="mt-4 space-y-3">
                            @foreach($reviews as $review)
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ $review->user->name ?? Str::before($review->user->email, '@') }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $review->created_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-0.5 text-amber-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <svg class="h-4 w-4 fill-amber-400" viewBox="0 0 20 20"><path d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
                                                @else
                                                    <svg class="h-4 w-4 fill-slate-200" viewBox="0 0 20 20"><path d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $review->comment }}</p>

                                    @if($review->seller_reply)
                                        <div class="mt-3 rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-xs text-emerald-900">
                                            <p class="font-semibold mb-1">Respon penjual</p>
                                            <p class="whitespace-pre-line">{{ $review->seller_reply }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-xs text-slate-600">Belum ada ulasan untuk produk ini.</p>
                    @endif
                </div>
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

