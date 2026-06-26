<x-suuq-layout>
    <x-slot name="title">{{ $product->name }} | سوق SUUQ</x-slot>
    <x-slot name="description">{{ Str::limit(strip_tags($product->description), 155) }}</x-slot>

    {{-- Breadcrumb --}}
    <nav class="text-sm breadcrumbs mb-6" aria-label="التنقل">
        <ul>
            <li><a href="{{ route('products.index') }}" class="text-primary font-semibold hover:underline">الرئيسية</a></li>
            @if($product->category)
                <li>
                    <a href="{{ route('products.index', ['category' => $product->category->id]) }}"
                       class="text-base-content/60 hover:text-primary">
                        {{ $product->category->name }}
                    </a>
                </li>
            @endif
            <li class="text-base-content font-bold truncate max-w-[200px]">{{ $product->name }}</li>
        </ul>
    </nav>

    {{-- ===== MAIN PRODUCT CARD ===== --}}
    <div class="bg-base-100 rounded-2xl border border-base-200 shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- === COLUMN 1: Images Carousel === --}}
            <div class="bg-base-200 p-6 flex flex-col items-center justify-center min-h-[320px] md:min-h-[480px]">
                @php
                    $covers = json_decode($product->covers, true);
                    $images = !empty($covers) ? $covers : ['https://images.unsplash.com/photo-1560343090-f0409e92791a?w=600&auto=format&fit=crop'];
                @endphp

                <div class="carousel w-full rounded-xl overflow-hidden shadow-md max-w-sm bg-base-100 relative">
                    @foreach($images as $index => $img)
                        <div id="slide-{{ $index }}" class="carousel-item relative w-full h-80">
                            <img src="{{ $img }}" alt="{{ $product->name }} - صورة {{ $index + 1 }}"
                                 class="w-full h-full object-cover" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" />
                            @if(count($images) > 1)
                                <div class="absolute flex justify-between transform -translate-y-1/2 left-4 right-4 top-1/2 z-10">
                                    <a href="#slide-{{ ($index - 1 + count($images)) % count($images) }}"
                                       class="btn btn-circle btn-sm btn-neutral opacity-60 hover:opacity-100"
                                       aria-label="الصورة السابقة">❮</a>
                                    <a href="#slide-{{ ($index + 1) % count($images) }}"
                                       class="btn btn-circle btn-sm btn-neutral opacity-60 hover:opacity-100"
                                       aria-label="الصورة التالية">❯</a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if(count($images) > 1)
                    <div class="flex justify-center gap-1.5 mt-3">
                        @foreach($images as $index => $img)
                            <a href="#slide-{{ $index }}"
                               class="btn btn-xs btn-circle btn-ghost border border-base-300 hover:bg-primary/20 hover:border-primary"
                               aria-label="الانتقال للصورة {{ $index + 1 }}">
                                {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- === COLUMN 2: Product Data & Actions === --}}
            <div class="p-6 md:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-4">

                    {{-- Category & stock --}}
                    <div class="flex items-center justify-between">
                        @if($product->category)
                            <a href="{{ route('products.index', ['category' => $product->category->id]) }}"
                               class="text-xs font-bold text-primary tracking-wider uppercase hover:underline">
                                {{ $product->category->name }}
                            </a>
                        @endif

                        @if($product->stock > 0)
                            <span class="badge badge-success badge-sm font-semibold text-white gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                متوفر ({{ $product->stock }})
                            </span>
                        @else
                            <span class="badge badge-error badge-sm font-semibold text-white">نفدت الكمية</span>
                        @endif
                    </div>

                    {{-- Title + Like --}}
                    <div class="flex items-start justify-between gap-3">
                        <h1 class="text-2xl font-black text-base-content leading-snug">{{ $product->name }}</h1>
                        @livewire('like-button', ['productId' => $product->id, 'isLiked' => $isLiked], key('like-show-'.$product->id))
                    </div>

                    {{-- Rating overview --}}
                    <div class="flex items-center gap-2">
                        <div class="rating rating-sm">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" disabled
                                       class="mask mask-star-2 {{ $i <= $ratingsStats['avg'] ? 'bg-orange-400' : 'bg-base-300' }}" />
                            @endfor
                        </div>
                        <span class="text-sm text-base-content/70 font-bold font-mono">{{ $ratingsStats['avg'] }}</span>
                        <span class="text-xs text-base-content/40">|</span>
                        <span class="text-xs text-base-content/60 font-bold">{{ $ratingsStats['count'] }} تقييم</span>
                    </div>

                    {{-- Price --}}
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-black text-secondary font-mono">{{ number_format($product->price, 2) }}</span>
                        <span class="text-base font-bold text-secondary">ر.س</span>
                        <span class="text-xs text-base-content/50">شامل ضريبة القيمة المضافة</span>
                    </div>

                    {{-- Short description --}}
                    <p class="text-sm text-base-content/70 leading-relaxed">
                        {{ Str::limit(strip_tags($product->description), 180) }}
                    </p>

                    {{-- Dummy Variants (Color/Size) --}}
                    <div class="space-y-3" x-data="{ color: '', size: '' }">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-base-content/60 min-w-[50px]">اللون:</span>
                            <div class="flex gap-2">
                                @foreach(['أسود' => 'bg-black', 'فضي' => 'bg-slate-300', 'أزرق' => 'bg-blue-600'] as $name => $colorClass)
                                    <button @click="color = '{{ $name }}'"
                                            :class="color === '{{ $name }}' ? 'border-primary ring-2 ring-primary/20 scale-110' : 'border-transparent'"
                                            class="w-7 h-7 rounded-full {{ $colorClass }} border-2 transition-all"
                                            title="{{ $name }}"
                                            :aria-pressed="color === '{{ $name }}'"></button>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-base-content/60 min-w-[50px]">المقاس:</span>
                            <div class="flex gap-2">
                                @foreach(['S', 'M', 'L', 'XL'] as $s)
                                    <button @click="size = '{{ $s }}'"
                                            :class="size === '{{ $s }}' ? 'btn-neutral' : 'btn-outline border-base-300'"
                                            class="btn btn-xs rounded-lg px-3 font-mono font-bold transition-all">
                                        {{ $s }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ADD TO CART - Livewire Island --}}
                <div class="border-t border-base-200 pt-5">
                    @livewire('add-to-cart', ['productId' => $product->id, 'stock' => $product->stock], key('cart-show-'.$product->id))
                </div>
            </div>
        </div>

        {{-- ===== TABS: DESCRIPTION & REVIEWS ===== --}}
        <div class="border-t border-base-200" x-data="{ activeTab: 'description' }">
            {{-- Tab buttons --}}
            <div class="flex bg-base-200/50 p-1 rounded-lg m-6 mb-4">
                <button class="flex-1 py-2.5 rounded-md text-sm font-bold transition-all"
                        :class="activeTab === 'description' ? 'bg-base-100 text-primary shadow-sm' : 'text-base-content/60 hover:text-base-content'"
                        @click="activeTab = 'description'">
                    الوصف الكامل
                </button>
                <button class="flex-1 py-2.5 rounded-md text-sm font-bold transition-all"
                        :class="activeTab === 'reviews' ? 'bg-base-100 text-primary shadow-sm' : 'text-base-content/60 hover:text-base-content'"
                        @click="activeTab = 'reviews'">
                    التقييمات والتعليقات ({{ $ratingsStats['count'] }})
                </button>
            </div>

            {{-- DESCRIPTION TAB --}}
            <div x-show="activeTab === 'description'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-6 pt-2 text-sm text-base-content/80 leading-relaxed text-right space-y-4">

                <p>{{ $product->description }}</p>

                <div class="border-t border-base-200 pt-4">
                    <h3 class="font-bold text-base-content mb-3">المواصفات الأساسية</h3>
                    <table class="table table-sm bg-base-200/40 rounded-xl overflow-hidden w-full max-w-lg">
                        <tbody>
                            <tr>
                                <td class="font-bold text-base-content/60 w-1/3">القسم الرئيسي</td>
                                <td>{{ $product->category ? $product->category->name : 'غير مصنف' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold text-base-content/60">حالة التوفر</td>
                                <td>{{ $product->stock > 0 ? "متوفر ({$product->stock} قطعة)" : 'نفدت الكمية' }}</td>
                            </tr>
                            <tr>
                                <td class="font-bold text-base-content/60">تاريخ الإضافة</td>
                                <td>{{ $product->created_at->format('Y/m/d') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- REVIEWS TAB --}}
            <div x-show="activeTab === 'reviews'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-6 pt-2 space-y-6">

                {{-- Ratings Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center bg-base-200/40 p-5 rounded-2xl border border-base-200">
                    <div class="text-center md:border-l md:border-base-300">
                        <span class="text-5xl font-black text-base-content font-mono">{{ $ratingsStats['avg'] ?: '—' }}</span>
                        <p class="text-xs text-base-content/50 font-bold mt-1">من أصل 5 نجوم</p>
                        <div class="rating rating-sm justify-center mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" disabled
                                       class="mask mask-star-2 {{ $i <= $ratingsStats['avg'] ? 'bg-orange-400' : 'bg-base-300' }}" />
                            @endfor
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        @foreach([5, 4, 3, 2, 1] as $star)
                            <div class="flex items-center gap-3 text-xs">
                                <span class="font-bold text-base-content/70 w-12 text-left font-mono">{{ $star }} ★</span>
                                <progress class="progress progress-warning flex-1"
                                          value="{{ $ratingsStats['percents'][$star] }}" max="100"></progress>
                                <span class="font-bold text-base-content/60 w-10 font-mono text-left">{{ $ratingsStats['percents'][$star] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ADD COMMENT - Livewire Island --}}
                @livewire('add-comment', ['productId' => $product->id], key('comment-'.$product->id))

                {{-- Comments List (Static - refreshed on page load) --}}
                <div class="space-y-4"
                     x-data="{}"
                     x-on:comment-added.window="window.location.reload()">
                    <h3 class="font-bold text-base text-base-content border-b border-base-200 pb-2">
                        آراء وتجارب العملاء
                    </h3>

                    @if($product->comments->isEmpty())
                        <p class="text-sm text-base-content/50 text-center py-8">
                            لا توجد تعليقات أو مراجعات لهذا المنتج بعد. كن أول من يضيف تقييمه!
                        </p>
                    @else
                        <div class="space-y-4 max-h-[480px] overflow-y-auto pr-1">
                            @foreach($product->comments as $comment)
                                <div class="flex gap-3 bg-base-200/30 p-4 rounded-xl border border-base-200/50">
                                    <div class="avatar placeholder flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-neutral text-neutral-content flex items-center justify-center font-bold text-xs">
                                            {{ $comment->user ? $comment->user->initials() : 'ع' }}
                                        </div>
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <h4 class="font-bold text-sm text-base-content">
                                                {{ $comment->user ? $comment->user->name : 'عميل سوق' }}
                                            </h4>
                                            <span class="text-[10px] text-base-content/40 font-bold font-mono">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="rating rating-xs">
                                            @for($i = 1; $i <= 5; $i++)
                                                <input type="radio" disabled
                                                       class="mask mask-star-2 {{ $i <= $comment->star ? 'bg-orange-400' : 'bg-base-300' }}" />
                                            @endfor
                                        </div>
                                        @if($comment->body)
                                            <p class="text-xs text-base-content/80 leading-relaxed mt-2 text-right">
                                                {{ $comment->body }}
                                            </p>
                                        @else
                                            <p class="text-xs text-base-content/30 italic mt-2">
                                                تقييم بالنجوم دون تعليق.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RELATED PRODUCTS ===== --}}
    @if($relatedProducts->isNotEmpty())
        <section class="mt-10" aria-labelledby="related-heading">
            <h2 id="related-heading" class="text-xl font-black text-base-content mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                منتجات قد تعجبك أيضاً
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                @foreach($relatedProducts as $related)
                    @php
                        $rCovers = json_decode($related->covers, true);
                        $rImage  = $rCovers[0] ?? 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=400&auto=format&fit=crop';
                    @endphp
                    <article class="card bg-base-100 shadow-sm hover:shadow-lg border border-base-200 transition-all duration-300 group rounded-xl overflow-hidden">
                        <figure class="h-40 overflow-hidden bg-base-200">
                            <a href="{{ route('products.show', $related->slug) }}">
                                <img src="{{ $rImage }}" alt="{{ $related->name }}"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                     loading="lazy" />
                            </a>
                        </figure>
                        <div class="card-body p-3 space-y-1">
                            <a href="{{ route('products.show', $related->slug) }}"
                               class="font-bold text-sm text-base-content line-clamp-1 hover:text-primary transition-colors">
                                {{ $related->name }}
                            </a>
                            <span class="text-sm font-black text-secondary font-mono">
                                {{ number_format($related->price, 2) }} ر.س
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

</x-suuq-layout>
