<x-storefront-layout>
    <div class="grid gap-6 lg:grid-cols-12">
        <section class="lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-lg font-semibold">Detail Pesanan</h1>
                        <p class="mt-1 text-sm text-slate-600">Order: <span class="font-semibold">{{ $order->order_number }}</span></p>
                        <p class="mt-1 text-sm text-slate-600">
                            Status:
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                        <p class="mt-1 text-sm text-slate-600">Pembayaran: <span class="font-semibold">{{ $order->payment?->status ?? 'pending' }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-600">Total</p>
                        <p class="text-xl font-bold">{{ $order->displayGrandTotal() }}</p>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                    <h2 class="text-sm font-semibold">Alamat Pengiriman</h2>
                    <p class="mt-2 text-sm text-slate-700">
                        <span class="font-semibold">{{ $order->shipping_recipient_name }}</span> · {{ $order->shipping_phone }}<br>
                        {{ $order->shipping_address_line }}<br>
                        {{ $order->shipping_city_name }}, {{ $order->shipping_province_name }} {{ $order->shipping_postal_code }}
                    </p>
                    <p class="mt-2 text-sm text-slate-600">
                        Kurir: <span class="font-semibold">{{ $order->courier }}</span> · Layanan: <span class="font-semibold">{{ $order->courier_service }}</span>
                        @if($order->courier_etd)
                            · Estimasi: <span class="font-semibold">{{ $order->courier_etd }}</span>
                        @endif
                    </p>
                </div>

                <div class="mt-6">
                    <h2 class="text-sm font-semibold">Item</h2>
                    <div class="mt-3 divide-y divide-slate-200 rounded-2xl border border-slate-200">
                        @foreach($order->items as $item)
                            <div class="p-4 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold">{{ $item->product_name }}</p>
                                        <p class="mt-1 text-slate-600">{{ $item->qty }} x Rp {{ number_format($item->price_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="font-semibold">Rp {{ number_format($item->line_total_amount, 0, ',', '.') }}</p>
                                </div>

                                @if($order->status === 'selesai' && $item->product)
                                    <div class="mt-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs" x-data="{ rating: 5 }" id="item-{{ $item->id }}">
                                        <p class="mb-2 font-semibold text-slate-700">Beri ulasan untuk produk ini</p>
                                        <form action="{{ route('products.reviews.store', [$item->product], false) }}" method="POST" class="grid gap-2">
                                            @csrf
                                            <input type="hidden" name="rating" x-model="rating">
                                            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}#item-{{ $item->id }}">
                                            <div class="flex items-center gap-2">
                                                <span class="text-slate-700">Rating:</span>
                                                <div class="flex items-center gap-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <button
                                                            type="button"
                                                            class="focus:outline-none"
                                                            @click="rating = {{ $i }}"
                                                        >
                                                            <svg
                                                                class="h-4 w-4 transition"
                                                                :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-slate-300 hover:text-amber-300'"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                            >
                                                                <path d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/>
                                                            </svg>
                                                        </button>
                                                    @endfor
                                                </div>
                                            </div>
                                            <textarea
                                                name="comment"
                                                rows="2"
                                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-slate-400 focus:ring-0"
                                                placeholder="Ceritakan pengalamanmu dengan produk ini..."></textarea>
                                            <div class="flex justify-end">
                                                <button class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                    Kirim ulasan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <aside class="lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-semibold">Ringkasan</h2>
                <div class="mt-4 grid gap-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Ongkir</span>
                        <span class="font-semibold">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-3">
                        <span class="font-semibold">Total</span>
                        <span class="text-lg font-bold">Rp {{ number_format($order->grand_total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if(($order->payment?->status ?? 'pending') === 'pending' && $order->payment?->snap_token)
                    <div class="mt-5">
                        <a href="{{ route('payments.pay', [$order], false) }}" class="block rounded-xl bg-slate-900 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-slate-800">
                            Bayar Sekarang
                        </a>
                    </div>
                @endif

                @if(in_array($order->status, ['diproses', 'dikirim'], true))
                    <div class="mt-3">
                        <form action="{{ route('orders.confirm', [$order], false) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa pesanan sudah Anda terima?');">
                            @csrf
                            @method('PATCH')
                            <button class="block w-full rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-900 hover:bg-emerald-100">
                                Pesanan sudah saya terima
                            </button>
                        </form>
                    </div>
                @elseif($order->status === 'selesai')
                    <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-900">
                        Pesanan ini sudah dikonfirmasi <span class="font-semibold">diterima</span>. Terima kasih sudah berbelanja di CosplayerWardrobe!
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('orders.invoice', [$order], false) }}" class="mb-2 block rounded-xl border border-slate-200 bg-white px-4 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                        Download Invoice (PDF)
                    </a>
                    <a href="{{ route('orders.index', [], false) }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                        Kembali
                    </a>
                </div>
            </div>
        </aside>
    </div>
</x-storefront-layout>

