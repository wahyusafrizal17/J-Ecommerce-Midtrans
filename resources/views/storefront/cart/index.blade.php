<x-storefront-layout>
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('error') }}
        </div>
    @endif
    @if(session('status'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif
    <div class="grid gap-6 lg:grid-cols-12">
        <section class="lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-200 p-5">
                    <h1 class="text-lg font-semibold">Keranjang</h1>
                    <p class="mt-1 text-sm text-slate-600">Periksa item sebelum checkout.</p>
                </div>

                <div class="divide-y divide-slate-200">
                    @forelse($cart->items as $item)
                        <div class="flex gap-4 p-5">
                            <div class="h-20 w-20 overflow-hidden rounded-xl bg-slate-100">
                                @if($item->product?->primaryImage)
                                    <img src="{{ $item->product->primaryImage->url() }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold">{{ $item->product?->name }}</p>
                                        <p class="mt-1 text-sm text-slate-600">{{ $item->product?->displayPrice() }}</p>
                                    </div>
                                    <form action="{{ route('cart.items.destroy', [$item], false) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs hover:bg-slate-50">Hapus</button>
                                    </form>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <form action="{{ route('cart.items.update', [$item], false) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input name="qty" type="number" min="1" max="99" value="{{ $item->qty }}" class="w-24 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                                        <button class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">Update</button>
                                    </form>

                                    <p class="text-sm font-semibold">
                                        Rp {{ number_format($item->lineTotalAmount(), 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-slate-600">
                            Keranjang kosong. <a href="{{ route('products.index', [], false) }}" class="font-semibold text-slate-900 hover:underline">Belanja sekarang</a>.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <aside class="lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-semibold">Ringkasan</h2>

                @php
                    $subtotal = (int) $cart->items->sum(fn ($i) => $i->lineTotalAmount());
                @endphp

                <div class="mt-4 grid gap-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Ongkir</span>
                        <span class="text-slate-500">Dihitung saat checkout</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold">Total</span>
                            <span class="text-lg font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-3">
                    @if($cart->items->isNotEmpty())
                        @auth
                            <a href="{{ route('checkout.index', [], false) }}" class="rounded-xl bg-slate-900 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                Checkout
                            </a>
                        @else
                            <a href="{{ route('login', [], false) }}" class="rounded-xl bg-slate-900 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-slate-800">
                                Login untuk Checkout
                            </a>
                        @endauth
                    @endif
                    <a href="{{ route('products.index', [], false) }}" class="rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </aside>
    </div>
</x-storefront-layout>

