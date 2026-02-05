<x-admin-layout title="Ulasan Produk">
    <div class="rounded-2xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 p-5">
            <h1 class="text-lg font-semibold">Ulasan Produk</h1>
            <p class="mt-1 text-sm text-slate-600">
                Lihat ulasan pelanggan dan balas dengan respon penjual.
            </p>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($reviews as $review)
                <div class="grid gap-4 p-5 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <p class="text-sm font-semibold text-slate-900">
                            {{ $review->product->name }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Oleh: {{ $review->user->name ?? $review->user->email }} ·
                            {{ $review->created_at->format('d M Y H:i') }}
                        </p>
                        <div class="mt-2 flex items-center gap-1 text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <svg class="h-4 w-4 fill-amber-400" viewBox="0 0 20 20"><path d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
                                @else
                                    <svg class="h-4 w-4 fill-slate-200" viewBox="0 0 20 20"><path d="M10 15.27L16.18 19l-1.64-7.03L20 7.24l-7.19-.61L10 0 7.19 6.63 0 7.24l5.46 4.73L3.82 19z"/></svg>
                                @endif
                            @endfor
                            <span class="ml-1 text-xs text-slate-600">{{ $review->rating }}/5</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-700 whitespace-pre-line">
                            {{ $review->comment }}
                        </p>
                    </div>

                    <div class="md:col-span-8">
                        <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="grid gap-3">
                            @csrf
                            @method('PATCH')
                            <div class="grid gap-1">
                                <label class="text-xs font-semibold text-slate-700">Respon penjual</label>
                                <textarea
                                    name="seller_reply"
                                    rows="3"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:ring-0"
                                    placeholder="Tulis jawaban atau klarifikasi untuk pelanggan...">{{ old('seller_reply', $review->seller_reply) }}</textarea>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-3 text-xs">
                                    <label class="inline-flex items-center gap-1">
                                        <input type="radio" name="status" value="published" class="h-3 w-3"
                                               @checked(old('status', $review->status) === 'published')>
                                        <span class="text-slate-700">Tampil</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1">
                                        <input type="radio" name="status" value="hidden" class="h-3 w-3"
                                               @checked(old('status', $review->status) === 'hidden')>
                                        <span class="text-slate-700">Sembunyikan</span>
                                    </label>
                                </div>

                                <button class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                    Simpan respon
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-slate-600">
                    Belum ada ulasan.
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-200 p-5">
            {{ $reviews->links() }}
        </div>
    </div>
</x-admin-layout>

