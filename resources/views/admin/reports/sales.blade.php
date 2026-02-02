<x-admin-layout :header="'Laporan Penjualan'">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight">Laporan Penjualan</h1>
            <p class="mt-1 text-sm text-slate-600">Ringkasan omzet berdasarkan pesanan berstatus paid.</p>
        </div>
        <form method="GET" class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <div>
                <label class="text-xs font-semibold text-slate-600">Dari</label>
                <input type="date" name="from" value="{{ $from }}" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600">Sampai</label>
                <input type="date" name="to" value="{{ $to }}" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
            </div>
            <div class="flex gap-2">
                <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">Terapkan</button>
                <a href="{{ route('admin.reports.sales.export', ['from' => $from, 'to' => $to]) }}" class="rounded-2xl bg-[#7367f0] px-4 py-2 text-sm font-extrabold text-white hover:bg-[#645bd6]">
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
            <p class="text-xs font-semibold text-slate-500">Periode</p>
            <p class="mt-2 text-sm font-semibold">{{ $from }} → {{ $to }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
            <p class="text-xs font-semibold text-slate-500">Jumlah Order (Paid)</p>
            <p class="mt-2 text-2xl font-bold">{{ number_format($paidOrders->count()) }}</p>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
            <p class="text-xs font-semibold text-slate-500">Total Omzet</p>
            <p class="mt-2 text-2xl font-bold">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold">Omzet Harian</h2>
        </div>
        <div class="mt-4">
            <canvas id="dailyChart" height="90"></canvas>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">Order</th>
                <th class="px-4 py-3 text-left">Customer</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Total</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
            @foreach($paidOrders as $o)
                <tr>
                    <td class="px-4 py-3 font-semibold">
                        <a class="hover:underline" href="{{ route('admin.orders.show', $o) }}">{{ $o->order_number }}</a>
                    </td>
                    <td class="px-4 py-3">{{ $o->user?->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $o->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 font-semibold">Rp {{ number_format($o->grand_total_amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const daily = @js($daily);
        const labels = daily.map(x => x.date);
        const revenues = daily.map(x => x.revenue);
        const ctx = document.getElementById('dailyChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue',
                    data: revenues,
                    backgroundColor: 'rgba(115, 103, 240, 0.20)',
                    borderColor: '#7367f0',
                    borderWidth: 1,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { callback: (v) => 'Rp ' + Number(v).toLocaleString('id-ID') } }
                }
            }
        });
    </script>
</x-admin-layout>

