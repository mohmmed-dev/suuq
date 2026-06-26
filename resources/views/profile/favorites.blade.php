<x-app-layout>
    <div>
        {{-- Products grid --}}
        @if($products->isEmpty())
            <div class="bg-base-100 rounded-xl border border-base-200 shadow-sm p-12 text-center flex flex-col items-center">
                <div class="bg-base-200 p-4 rounded-full mb-4 text-base-content/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-base-content mb-2">لم نجد أي نتائج</h3>
                <a href="{{ route('products.index') }}" class="btn btn-primary rounded-lg btn-sm px-6">عرض جميع المنتجات</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach($products as $product)
                    @php
                        $covers  = json_decode($product->covers, true);
                        $image   = $covers[0] ?? 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=400&auto=format&fit=crop';
                        $starsAvg = round($product->comments->avg('star'), 1);
                        $reviewsCount = $product->comments->count();
                        $isLiked = true;
                    @endphp

                    <article class="card bg-base-100 shadow-sm hover:shadow-xl border border-base-200 transition-all duration-300 group rounded-xl overflow-hidden">

                        {{-- Image + Like button --}}
                        <figure class="relative h-52 overflow-hidden bg-base-200">
                            <a href="{{ route('products.show', $product->slug) }}">
                                <img src="{{ $image }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        loading="lazy" />
                            </a>

                            {{-- Livewire Like Island --}}
                            <div class="absolute top-3 right-3">
                                @livewire('like-button', ['productId' => $product->id, 'isLiked' => $isLiked], key('like-'.$product->id))
                            </div>

                            {{-- Category badge --}}
                            <span class="badge badge-neutral badge-sm absolute bottom-3 right-3 font-semibold shadow-md">
                                {{ $product->category ? $product->category->name : 'غير مصنف' }}
                            </span>

                            @if($product->stock <= 0)
                                <div class="absolute inset-0 bg-base-300/60 backdrop-blur-[1px] flex items-center justify-center">
                                    <span class="badge badge-error badge-lg font-bold shadow-lg">نفدت الكمية</span>
                                </div>
                            @elseif($product->stock <= 5)
                                <span class="badge badge-warning badge-sm absolute bottom-3 left-3 font-semibold shadow-md">آخر {{ $product->stock }} قطع</span>
                            @endif
                        </figure>

                        {{-- Card body --}}
                        <div class="card-body p-4 space-y-2">
                            <a href="{{ route('products.show', $product->slug) }}"
                                class="card-title text-base font-black text-base-content line-clamp-1 hover:text-primary transition-colors">
                                {{ $product->name }}
                            </a>

                            {{-- Stars --}}
                            <div class="flex items-center gap-1.5">
                                <div class="rating rating-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" disabled
                                                class="mask mask-star-2 {{ $i <= $starsAvg ? 'bg-orange-400' : 'bg-base-300' }}" />
                                    @endfor
                                </div>
                                @if($reviewsCount > 0)
                                    <span class="text-xs text-base-content/50 font-bold">({{ $reviewsCount }})</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <span class="text-base font-black text-secondary font-mono">
                                    {{ number_format($product->price, 2) }} ر.س
                                </span>

                                <div class="flex gap-2">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                        class="btn btn-outline btn-sm btn-square rounded-lg hover:btn-primary"
                                        title="تفاصيل المنتج"
                                        aria-label="عرض تفاصيل المنتج">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Livewire AddToCart Island (compact: button only) --}}
                                    @livewire('add-to-cart', ['productId' => $product->id, 'stock' => $product->stock, 'compact' => true], key('cart-card-'.$product->id))
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8 flex justify-center font-bold">
                {{ $products->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
</x-app-layout>
