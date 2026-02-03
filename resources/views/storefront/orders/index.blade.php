<x-storefront-layout>
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 p-5">
            <h1 class="text-lg font-semibold">Riwayat Pesanan</h1>
            <p class="mt-1 text-sm text-slate-600">Lihat status pesanan dan pembayaran.</p>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($orders as $order)
                <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold">{{ $order->order_number }}</p>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ $order->created_at->format('d M Y H:i') }} · Status: <span class="font-semibold">{{ $order->status }}</span>
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Pembayaran: <span class="font-semibold">{{ $order->payment?->status ?? 'pending' }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <p class="text-sm font-bold">{{ $order->displayGrandTotal() }}</p>
                        <a href="{{ route('orders.show', [$order], false) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">
                            Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-slate-600">
                    Belum ada pesanan.
                </div>
            @endforelse
        </div>

        <div class="p-5">
            {{ $orders->links() }}
        </div>
    </div>
</x-storefront-layout>

