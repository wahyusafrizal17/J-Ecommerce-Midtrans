<x-storefront-layout>
    <div class="mx-auto grid max-w-2xl gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold">Pembayaran</h1>
                    <p class="mt-1 text-sm text-slate-600">Order: <span class="font-semibold">{{ $order->order_number }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-600">Total</p>
                    <p class="text-xl font-bold">{{ $order->displayGrandTotal() }}</p>
                </div>
            </div>

            @if(blank($clientKey))
                <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    Midtrans belum dikonfigurasi. Isi <span class="font-semibold">MIDTRANS_CLIENT_KEY</span> dan <span class="font-semibold">MIDTRANS_SERVER_KEY</span> di <code>.env</code>.
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    Klik tombol di bawah untuk membuka Midtrans Snap.
                </div>
            @endif

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button id="pay-button" @disabled(blank($clientKey)) class="flex-1 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60">
                    Bayar Sekarang
                </button>
                <a href="{{ route('orders.show', [$order], false) }}" class="flex-1 rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                    Lihat Detail Order
                </a>
            </div>
        </div>
    </div>

    @php
        $snapJs = config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp

    @if(!blank($clientKey))
        <script src="{{ $snapJs }}" data-client-key="{{ $clientKey }}"></script>
        <script>
            const token = @js($order->payment->snap_token);
            document.getElementById('pay-button').addEventListener('click', function () {
                window.snap.pay(token, {
                    onSuccess: function () {
                        window.location.href = @js(route('orders.show', [$order], false));
                    },
                    onPending: function () {
                        window.location.href = @js(route('orders.show', [$order], false));
                    },
                    onError: function () {
                        window.location.href = @js(route('orders.show', [$order], false));
                    },
                    onClose: function () {
                        // user closed without finishing
                    }
                });
            });
        </script>
    @endif
</x-storefront-layout>

