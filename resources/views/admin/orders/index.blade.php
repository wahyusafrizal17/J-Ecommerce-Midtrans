<x-admin-layout :header="'Pesanan'">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight">Pesanan</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola dan update status pesanan.</p>
        </div>
        <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                <option value="">Semua status</option>
                @foreach(['pending','diproses','dikirim','selesai','dibatalkan'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <input name="q" value="{{ $q }}" placeholder="Order no / user..." class="w-64 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
            <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Filter</button>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">Order</th>
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Pembayaran</th>
                <th class="px-4 py-3 text-left">Total</th>
                <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach($orders as $o)
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $o->order_number }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $o->created_at->format('d M Y H:i') }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $o->user?->name }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $o->user?->email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $o->status }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="rounded-full {{ ($o->payment?->status ?? 'pending') === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }} px-2 py-1 text-xs font-semibold">
                            {{ $o->payment?->status ?? 'pending' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($o->grand_total_amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.orders.show', $o) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold hover:bg-slate-50">Detail</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $orders->links() }}
    </div>
</x-admin-layout>

