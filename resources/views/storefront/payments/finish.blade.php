<x-storefront-layout>
    <div class="mx-auto grid max-w-2xl gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h1 class="text-lg font-semibold">Pembayaran Diproses</h1>
            <p class="mt-2 text-sm text-slate-600">
                Status pembayaran akan diperbarui otomatis. Kamu bisa cek detail pesanan di halaman riwayat.
            </p>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('orders.index', [], false) }}" class="flex-1 rounded-xl bg-slate-900 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-slate-800">
                    Riwayat Pesanan
                </a>
                <a href="{{ route('home') }}" class="flex-1 rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50">
                    Kembali ke Home
                </a>
            </div>
        </div>
    </div>
</x-storefront-layout>

