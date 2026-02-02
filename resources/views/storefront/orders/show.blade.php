<x-storefront-layout>
    <div class="grid gap-6 lg:grid-cols-12">
        <section class="lg:col-span-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-lg font-semibold">Detail Pesanan</h1>
                        <p class="mt-1 text-sm text-slate-600">Order: <span class="font-semibold">{{ $order->order_number }}</span></p>
                        <p class="mt-1 text-sm text-slate-600">Status: <span class="font-semibold">{{ $order->status }}</span></p>
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
                            <div class="flex items-start justify-between gap-3 p-4 text-sm">
                                <div>
                                    <p class="font-semibold">{{ $item->product_name }}</p>
                                    <p class="mt-1 text-slate-600">{{ $item->qty }} x Rp {{ number_format($item->price_amount, 0, ',', '.') }}</p>
                                </div>
                                <p class="font-semibold">Rp {{ number_format($item->line_total_amount, 0, ',', '.') }}</p>
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
                        <a href="{{ route('payments.pay', $order) }}" class="block rounded-xl bg-slate-900 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-slate-800">
                            Bayar Sekarang
                        </a>
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('orders.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                        Kembali
                    </a>
                </div>
            </div>
        </aside>
    </div>
</x-storefront-layout>

