<x-admin-layout :header="'Detail Pesanan'">
    <div class="grid gap-6 lg:grid-cols-12">
        <section class="lg:col-span-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight">{{ $order->order_number }}</h1>
                        <p class="mt-1 text-sm text-slate-600">Customer: <span class="font-semibold">{{ $order->user?->name }}</span> ({{ $order->user?->email }})</p>
                        <p class="mt-1 text-sm text-slate-600">Pembayaran: <span class="font-semibold">{{ $order->payment?->status ?? 'pending' }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-slate-600">Total</p>
                        <p class="text-xl font-bold">Rp {{ number_format($order->grand_total_amount, 0, ',', '.') }}</p>
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
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
                <h2 class="text-sm font-semibold">Update Status</h2>
                <p class="mt-1 text-sm text-slate-600">Status saat ini: <span class="font-semibold">{{ $order->status }}</span></p>

                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mt-4 grid gap-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                        @foreach(['pending','diproses','dikirim','selesai','dibatalkan'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-2xl bg-[#7367f0] px-4 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">Simpan</button>
                </form>

                <div class="mt-6 border-t border-slate-200 pt-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-slate-600">Ongkir</span>
                        <span class="font-semibold">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                        <span class="font-semibold">Total</span>
                        <span class="text-lg font-bold">Rp {{ number_format($order->grand_total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-5">
                    <a href="{{ route('admin.orders.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                        Kembali
                    </a>
                </div>
            </div>
        </aside>
    </div>
</x-admin-layout>

