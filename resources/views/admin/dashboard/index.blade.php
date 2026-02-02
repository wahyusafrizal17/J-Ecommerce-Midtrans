<x-admin-layout :header="'Dashboard'">
    <div class="grid gap-4 lg:grid-cols-12">
        <div class="lg:col-span-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold tracking-widest text-slate-500">TOTAL ORDER</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($totalOrders) }}</p>
                    <p class="mt-2 text-sm text-slate-600">Semua pesanan (all status).</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-[#7367f0]/10 text-[#7367f0]">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2">
                        <path d="M6 2h12v20l-2-1-2 1-2-1-2 1-2-1-2 1V2Z"/>
                        <path d="M9 7h6"/>
                        <path d="M9 11h6"/>
                        <path d="M9 15h6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold tracking-widest text-slate-500">TOTAL REVENUE</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="mt-2 text-sm text-slate-600">Hanya order berstatus paid.</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-500/10 text-emerald-600">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2">
                        <path d="M12 1v22"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold tracking-widest text-slate-500">TOTAL PRODUK</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">{{ number_format($totalProducts) }}</p>
                    <p class="mt-2 text-sm text-slate-600">Produk aktif & tersimpan.</p>
                </div>
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-sky-500/10 text-sky-600">
                    <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6" stroke="currentColor" stroke-width="2">
                        <path d="M21 8.5 12 3 3 8.5l9 5 9-5Z"/>
                        <path d="M3 8.5V20l9 5 9-5V8.5"/>
                        <path d="M12 13.5V21"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_10px_30px_rgba(15,23,42,0.06)]">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Grafik Penjualan (14 hari)</h2>
                <p class="mt-1 text-xs text-slate-500">Paid orders • Revenue trend</p>
            </div>
            <span class="rounded-full bg-[#7367f0]/10 px-3 py-1 text-xs font-extrabold text-[#7367f0]">Revenue</span>
        </div>
        <div class="mt-4">
            <canvas id="salesChart" height="90"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const series = @js($salesSeries);
        const labels = series.map(x => x.date);
        const data = series.map(x => x.revenue);

        const ctx = document.getElementById('salesChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue',
                    data,
                    borderColor: '#7367f0',
                    backgroundColor: 'rgba(115, 103, 240, 0.14)',
                    fill: true,
                    tension: 0.35,
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

