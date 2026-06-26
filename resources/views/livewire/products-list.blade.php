<div x-data="{ 
    toastMessage: '', 
    toastType: 'success', 
    showToast: false,
    triggerToast(msg, type) {
        this.toastMessage = msg;
        $wire.toastType = type;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 3000);
    }
}" 
x-on:show-toast-js.window="triggerToast($event.detail.message, $event.detail.type)">

    <!-- TOAST NOTIFICATION BANNER -->
    <div class="toast toast-top toast-start z-[9999]" x-show="showToast" x-transition style="display: none;">
        <div class="alert rounded-xl shadow-lg border text-white font-bold" 
             :class="{
                 'alert-success bg-success border-success': $wire.toastType === 'success',
                 'alert-error bg-error border-error': $wire.toastType === 'error',
                 'alert-info bg-info border-info': $wire.toastType === 'info'
             }">
            <span x-text="toastMessage"></span>
        </div>
    </div>

    <!-- HORIZONTAL CATEGORY CAROUSEL -->
    <div class="mb-8 overflow-x-auto pb-2 flex gap-3 scrollbar-hide">
        <button wire:click="selectCategory(null)" 
                class="btn rounded-full btn-sm px-6 font-bold flex-shrink-0 {{ is_null($selectedCategory) ? 'btn-primary' : 'btn-outline border-base-300 hover:border-primary' }}">
            الكل
        </button>
        @foreach($categories as $parentCat)
            <button wire:click="selectCategory({{ $parentCat->id }})" 
                    class="btn rounded-full btn-sm px-6 font-bold flex-shrink-0 {{ $selectedCategory == $parentCat->id ? 'btn-primary' : 'btn-outline border-base-300 hover:border-primary' }}">
                {{ $parentCat->name }}
            </button>
        @endforeach
    </div>

    <!-- GRID WITH SIDEBAR AND MAIN CONTENT -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        
        <!-- SIDEBAR FILTERS -->
        <div class="bg-base-100 p-6 rounded-xl border border-base-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-base-200 pb-3">
                <h3 class="font-black text-lg text-base-content flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    خيارات التصفية
                </h3>
                <button wire:click="resetFilters" class="text-xs text-error font-semibold hover:underline">إعادة ضبط</button>
            </div>

            <!-- Categories Accordion -->
            <div class="space-y-2">
                <h4 class="font-bold text-sm text-base-content mb-2">التصنيفات الفرعية</h4>
                <div class="menu menu-sm bg-base-200/50 rounded-lg p-2 max-h-60 overflow-y-auto">
                    @foreach($categories as $parentCat)
                        <details class="group" {{ $selectedCategory == $parentCat->id || in_array($selectedCategory, $parentCat->subcategory->pluck('id')->toArray()) ? 'open' : '' }}>
                            <summary class="font-bold text-base-content/80 group-open:text-primary">
                                {{ $parentCat->name }}
                            </summary>
                            <ul class="pr-2 border-r border-base-300/60 mt-1">
                                <li>
                                    <button wire:click="selectCategory({{ $parentCat->id }})" 
                                            class="{{ $selectedCategory == $parentCat->id ? 'active' : '' }}">
                                        جميع منتجات {{ $parentCat->name }}
                                    </button>
                                </li>
                                @foreach($parentCat->subcategory as $subCat)
                                    <li>
                                        <button wire:click="selectCategory({{ $subCat->id }})" 
                                                class="{{ $selectedCategory == $subCat->id ? 'active font-bold' : '' }}">
                                            {{ str_replace($parentCat->name . ' - ', '', $subCat->name) }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    @endforeach
                </div>
            </div>

            <!-- Price Range Filter -->
            <div class="space-y-4">
                <h4 class="font-bold text-sm text-base-content">نطاق السعر (ر.س)</h4>
                <div class="space-y-2">
                    <input type="range" min="0" max="2000" step="50" wire:model.live.debounce.300ms="maxPrice" class="range range-primary range-xs" />
                    <div class="flex justify-between items-center text-xs font-mono font-bold text-base-content/70">
                        <span>{{ $minPrice }} ر.س</span>
                        <span>إلى {{ $maxPrice }} ر.س</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN PRODUCTS GRID SECTION -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- TOP BAR (SORTING & INFO) -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-base-100 p-4 rounded-xl border border-base-200 shadow-sm">
                <div class="text-sm font-semibold text-base-content/70 text-right">
                    عثرنا على <span class="font-bold text-secondary font-mono">{{ $products->total() }}</span> منتج متوفر
                </div>
                
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-base-content/60">ترتيب حسب:</span>
                    <select wire:model.live="sortBy" class="select select-bordered select-sm rounded-lg text-xs font-bold">
                        <option value="created_at">الأحدث إضافة</option>
                        <option value="price_asc">السعر: من الأقل إلى الأعلى</option>
                        <option value="price_desc">السعر: من الأعلى إلى الأقل</option>
                        <option value="name">الاسم أبجدياً</option>
                    </select>
                </div>
            </div>

            <!-- SKELETON LOADER (Visible while products are loading) -->
            <div wire:loading wire:target="search, selectedCategory, minPrice, maxPrice, sortBy" class="w-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @for($i = 0; $i < 6; $i++)
                        <div class="card bg-base-100 shadow-md border border-base-200 animate-pulse">
                            <div class="h-48 bg-base-300 w-full rounded-t-xl"></div>
                            <div class="card-body p-4 space-y-3">
                                <div class="h-4 bg-base-300 rounded w-2/3"></div>
                                <div class="h-3 bg-base-300 rounded w-1/3"></div>
                                <div class="h-3 bg-base-300 rounded w-1/2"></div>
                                <div class="flex justify-between items-center pt-4">
                                    <div class="h-6 bg-base-300 rounded w-1/4"></div>
                                    <div class="h-8 bg-base-300 rounded w-1/3"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- ACTUAL PRODUCTS GRID -->
            <div wire:loading.remove wire:target="search, selectedCategory, minPrice, maxPrice, sortBy" class="w-full">
                @if($products->isEmpty())
                    <!-- Empty State -->
                    <div class="bg-base-100 rounded-xl border border-base-200 shadow-sm p-12 text-center flex flex-col items-center">
                        <div class="bg-base-200 p-4 rounded-full mb-4 text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-base-content mb-2">عذراً، لم نجد أي نتائج</h3>
                        <p class="text-sm text-base-content/60 mb-6">جرب تغيير كلمات البحث أو استخدام خيارات تصفية مختلفة.</p>
                        <button wire:click="resetFilters" class="btn btn-primary rounded-lg btn-sm px-6">عرض جميع المنتجات</button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="card bg-base-100 shadow-sm hover:shadow-xl border border-base-200 transition-all duration-300 group rounded-xl">
                                <!-- Card Image & Like -->
                                <figure class="relative h-48 overflow-hidden bg-base-200">
                                    <!-- Image -->
                                    @php
                                        $covers = json_decode($product->covers, true);
                                        $image = $covers[0] ?? 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=400&auto=format&fit=crop';
                                    @endphp
                                    <img src="{{ $image }}" alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                                         wire:click="viewDetails({{ $product->id }})" />
                                    
                                    <!-- Like button -->
                                    @php
                                        $isLiked = in_array($product->id, $likedProductIds);
                                    @endphp
                                    <button wire:click="toggleLike({{ $product->id }})" 
                                            class="btn btn-circle btn-sm bg-base-100/80 hover:bg-base-100 border-none absolute top-3 right-3 shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             class="h-5 w-5 {{ $isLiked ? 'fill-red-500 text-red-500' : 'text-base-content/40 group-hover:text-red-400' }}" 
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>

                                    <!-- Category Badge -->
                                    <span class="badge badge-neutral badge-sm absolute bottom-3 right-3 font-semibold shadow-md">
                                        {{ $product->category ? str_replace(' - فرعي أ', '', str_replace(' - فرعي ب', '', str_replace(' - فرعي ج', '', $product->category->name))) : 'غير مصنف' }}
                                    </span>
                                </figure>

                                <!-- Card Body -->
                                <div class="card-body p-4 justify-between space-y-2">
                                    <div>
                                        <h3 class="card-title text-base font-black text-base-content line-clamp-1 hover:text-primary cursor-pointer transition-colors" 
                                            wire:click="viewDetails({{ $product->id }})">
                                            {{ $product->name }}
                                        </h3>
                                        
                                        <!-- Reviews/Stars Summary -->
                                        @php
                                            $starsAvg = round($product->comments->avg('star'), 1);
                                            $reviewsCount = $product->comments->count();
                                        @endphp
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <div class="rating rating-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" disabled class="mask mask-star-2 {{ $i <= $starsAvg ? 'bg-orange-400' : 'bg-base-300' }}" />
                                                @endfor
                                            </div>
                                            @if($reviewsCount > 0)
                                                <span class="text-xs text-base-content/50 font-bold">({{ $reviewsCount }})</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <!-- Price -->
                                        <div class="flex flex-col">
                                            <span class="text-base font-black text-secondary font-mono">{{ number_format($product->price, 2) }} ر.س</span>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex gap-2">
                                            <button wire:click="viewDetails({{ $product->id }})" 
                                                    class="btn btn-outline btn-sm btn-square rounded-lg hover:btn-primary" 
                                                    title="تفاصيل المنتج">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button wire:click="addToCart({{ $product->id }})" 
                                                    class="btn btn-primary btn-sm rounded-lg font-bold gap-1 shadow-sm group-hover:shadow-primary/30">
                                                السلة
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- PAGINATION LINKS -->
                    <div class="mt-8 font-sans font-bold flex justify-center">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
