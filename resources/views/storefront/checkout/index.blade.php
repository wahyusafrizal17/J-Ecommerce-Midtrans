<x-storefront-layout>
    @php
        $subtotal = (int) $cart->items->sum(fn ($i) => (int) $i->qty * (int) $i->product->price_amount);
    @endphp

    <div x-data="checkoutForm({
        provinces: @js($provinces),
        defaultCourier: @js($defaultCourier),
        subtotal: @js($subtotal),
        citiesUrl: @js(route('checkout.cities', [], false)),
        districtsUrl: @js(route('checkout.districts', [], false)),
        costsUrl: @js(route('checkout.costs', [], false)),
        csrf: @js(csrf_token()),
        rajaOngkirReady: @js($rajaOngkirReady ?? false),
    })" class="grid gap-6 lg:grid-cols-12">
        <section class="lg:col-span-8">
            <form action="{{ route('checkout.store', [], false) }}" method="POST" class="rounded-2xl border border-slate-200 bg-white p-5">
                @csrf
                <h1 class="text-lg font-semibold">Checkout</h1>
                <p class="mt-1 text-sm text-slate-600">Lengkapi alamat pengiriman dan pilih layanan kurir.</p>

                @if(!($rajaOngkirReady ?? false))
                    <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        RajaOngkir belum dikonfigurasi. Isi <span class="font-semibold">RAJAONGKIR_API_KEY</span> dan
                        <span class="font-semibold">RAJAONGKIR_ORIGIN_DISTRICT_ID</span> agar ongkir bisa dihitung.
                    </div>
                @endif

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label class="text-xs font-semibold text-slate-600">Nama penerima</label>
                        <input name="recipient_name" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="text-xs font-semibold text-slate-600">No. HP</label>
                        <input name="phone" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Alamat lengkap</label>
                        <textarea name="address_line" rows="3" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0"></textarea>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Provinsi</label>
                        <select x-model="provinceId" @change="onProvinceChange()" name="province_id" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                            <option value="">Pilih provinsi</option>
                            <template x-for="p in provinces" :key="p.id">
                                <option :value="p.id" x-text="p.name"></option>
                            </template>
                        </select>
                        <input type="hidden" name="province_name" :value="provinceName">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Kota</label>
                        <select x-model="cityId" @change="onCityChange()" name="city_id" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0" :disabled="loadingCities || cities.length === 0">
                            <option value="">Pilih kota</option>
                            <template x-for="c in cities" :key="c.id">
                                <option :value="c.id" x-text="`${c.type} ${c.name}`"></option>
                            </template>
                        </select>
                        <input type="hidden" name="city_name" :value="cityName">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Kecamatan</label>
                        <select x-model="districtId" @change="onDistrictChange()" name="district_id" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0" :disabled="loadingDistricts || districts.length === 0">
                            <option value="">Pilih kecamatan</option>
                            <template x-for="d in districts" :key="d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                        <input type="hidden" name="district_name" :value="districtName">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Kode Pos</label>
                        <input name="postal_code" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Kurir</label>
                        <select x-model="courier" @change="fetchCosts()" name="courier" required class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0">
                            <option value="jne">JNE</option>
                            <option value="tiki">TIKI</option>
                            <option value="pos">POS</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-slate-600">Catatan (opsional)</label>
                        <textarea name="customer_note" rows="2" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0"></textarea>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold">Pilih Layanan Ongkir</h2>
                        <button type="button" @click="fetchCosts()" class="rounded-lg border border-slate-200 px-3 py-2 text-xs hover:bg-slate-50">
                            Hitung Ongkir
                        </button>
                    </div>

                    <div class="mt-3 text-sm text-slate-600" x-show="loadingCosts">Mengambil ongkir...</div>

                    <div class="mt-4 grid gap-2" x-show="!loadingCosts">
                        <template x-for="s in services" :key="s.service">
                            <label class="flex cursor-pointer items-start justify-between gap-3 rounded-xl border border-slate-200 p-3 hover:bg-slate-50">
                                <div>
                                    <p class="text-sm font-semibold" x-text="`${s.service} - ${s.description}`"></p>
                                    <p class="mt-1 text-xs text-slate-600">
                                        Estimasi: <span x-text="s.etd ? s.etd : '-'"></span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <p class="text-sm font-bold" x-text="formatRupiah(s.cost)"></p>
                                    <input type="radio" name="courier_service" :value="s.service" x-model="selectedService" @change="selectedCost = s.cost" required class="mt-1">
                                </div>
                            </label>
                        </template>
                        <div x-show="services.length === 0 && districtId" class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">
                            Tidak ada layanan tersedia. Coba kurir lain.
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <button class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                        Buat Pesanan & Bayar
                    </button>
                </div>
            </form>
        </section>

        <aside class="lg:col-span-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                <h2 class="text-sm font-semibold">Ringkasan Pesanan</h2>

                <div class="mt-4 grid gap-3">
                    @foreach($cart->items as $item)
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{{ $item->product->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-600">{{ $item->qty }} x Rp {{ number_format($item->product->price_amount, 0, ',', '.') }}</p>
                            </div>
                            <p class="font-semibold">Rp {{ number_format($item->qty * $item->product->price_amount, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 border-t border-slate-200 pt-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-slate-600">Ongkir</span>
                        <span class="font-semibold" x-text="selectedCost ? formatRupiah(selectedCost) : '-'"></span>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                        <span class="font-semibold">Total</span>
                        <span class="text-lg font-bold" x-text="formatRupiah(total())"></span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        function checkoutForm({provinces, defaultCourier, subtotal, citiesUrl, districtsUrl, costsUrl, csrf, rajaOngkirReady}) {
            return {
                provinces,
                subtotal,
                rajaOngkirReady,
                provinceId: '',
                provinceName: '',
                cities: [],
                cityId: '',
                cityName: '',
                districts: [],
                districtId: '',
                districtName: '',
                courier: defaultCourier || 'jne',
                services: [],
                selectedService: '',
                selectedCost: 0,
                loadingCities: false,
                loadingDistricts: false,
                loadingCosts: false,

                formatRupiah(amount) {
                    const n = Number(amount || 0);
                    return 'Rp ' + n.toLocaleString('id-ID');
                },
                total() {
                    return Number(this.subtotal) + Number(this.selectedCost || 0);
                },
                async onProvinceChange() {
                    const p = this.provinces.find(x => x.id === this.provinceId);
                    this.provinceName = p ? p.name : '';
                    this.cityId = '';
                    this.cityName = '';
                    this.districtId = '';
                    this.districtName = '';
                    this.districts = [];
                    this.services = [];
                    this.selectedService = '';
                    this.selectedCost = 0;

                    if (!this.provinceId) {
                        this.cities = [];
                        return;
                    }

                    this.loadingCities = true;
                    try {
                        const url = new URL(citiesUrl, window.location.origin);
                        url.searchParams.set('province_id', this.provinceId);
                        const res = await fetch(url.toString(), {headers: {'Accept': 'application/json'}});
                        this.cities = await res.json();
                    } finally {
                        this.loadingCities = false;
                    }
                },
                async onCityChange() {
                    const c = this.cities.find(x => x.id === this.cityId);
                    this.cityName = c ? `${c.type} ${c.name}` : '';

                    this.districtId = '';
                    this.districtName = '';
                    this.districts = [];
                    this.services = [];
                    this.selectedService = '';
                    this.selectedCost = 0;

                    if (!this.cityId) return;

                    this.loadingDistricts = true;
                    try {
                        const url = new URL(districtsUrl, window.location.origin);
                        url.searchParams.set('city_id', this.cityId);
                        const res = await fetch(url.toString(), {headers: {'Accept': 'application/json'}});
                        this.districts = await res.json();
                    } finally {
                        this.loadingDistricts = false;
                    }
                },
                async onDistrictChange() {
                    const d = this.districts.find(x => x.id === this.districtId);
                    this.districtName = d ? d.name : '';
                    await this.fetchCosts();
                },
                async fetchCosts() {
                    if (!this.rajaOngkirReady) return;
                    if (!this.districtId) return;
                    this.loadingCosts = true;
                    try {
                        const res = await fetch(costsUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                district_id: this.districtId,
                                courier: this.courier,
                            }),
                        });
                        this.services = await res.json();
                        this.selectedService = '';
                        this.selectedCost = 0;
                    } finally {
                        this.loadingCosts = false;
                    }
                },
            }
        }

    </script>
</x-storefront-layout>

