<div class="modal {{ $isOpen ? 'modal-open' : '' }} z-[150]" role="dialog">
    <div class="modal-box max-w-4xl w-11/12 bg-base-100 rounded-2xl shadow-2xl p-0 overflow-hidden relative border border-base-200">
        
        @if($product)
            <!-- Close Button (Top Left/Right) -->
            <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost absolute left-4 top-4 z-50 text-base-content/60 hover:text-base-content">✕</button>

            <!-- Modal Toast Banner -->
            @if($modalToast)
                <div class="absolute top-4 right-4 z-[100] alert alert-success rounded-lg shadow-md max-w-xs text-xs font-bold text-white bg-success border-success animate-bounce">
                    <span>{{ $modalToast }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2">
                
                <!-- COLUMN 1: PRODUCT COVERS CAROUSEL -->
                <div class="bg-base-200 p-6 flex flex-col items-center justify-center relative min-h-[300px] md:min-h-[400px]">
                    @php
                        $covers = json_decode($product->covers, true);
                        $images = !empty($covers) ? $covers : ['https://images.unsplash.com/photo-1560343090-f0409e92791a?w=600&auto=format&fit=crop'];
                    @endphp
                    
                    <!-- Carousel -->
                    <div class="carousel w-full rounded-xl overflow-hidden shadow-md max-w-xs relative bg-base-100">
                        @foreach($images as $index => $img)
                            <div id="slide-{{ $index }}" class="carousel-item relative w-full h-72">
                                <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2 z-[5]">
                                    <a href="#slide-{{ ($index - 1 + count($images)) % count($images) }}" class="btn btn-circle btn-xs btn-neutral opacity-50 hover:opacity-100">❮</a>
                                    <a href="#slide-{{ ($index + 1) % count($images) }}" class="btn btn-circle btn-xs btn-neutral opacity-50 hover:opacity-100">❯</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Slide Indicators -->
                    <div class="flex justify-center w-full py-2 gap-1.5 mt-2">
                        @foreach($images as $index => $img)
                            <a href="#slide-{{ $index }}" class="btn btn-xs btn-circle btn-ghost border border-base-300 hover:bg-primary/20 hover:border-primary">{{ $index + 1 }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- COLUMN 2: PRODUCT DATA & INTERACTION -->
                <div class="p-6 md:p-8 flex flex-col justify-between space-y-6">
                    <div>
                        <!-- Category & Stock Status -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-primary tracking-wider uppercase">
                                {{ $product->category ? str_replace(' - فرعي أ', '', str_replace(' - فرعي ب', '', str_replace(' - فرعي ج', '', $product->category->name))) : 'غير مصنف' }}
                            </span>
                            @if($product->stock > 0)
                                <span class="badge badge-success badge-sm font-semibold gap-1 text-white">متوفر في المخزون ({{ $product->stock }})</span>
                            @else
                                <span class="badge badge-error badge-sm font-semibold text-white">نفدت الكمية</span>
                            @endif
                        </div>

                        <!-- Product Title -->
                        <h2 class="text-2xl font-black text-base-content mb-2">{{ $product->name }}</h2>

                        <!-- Rating Overview -->
                        <div class="flex items-center gap-2 mb-4">
                            <div class="rating rating-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" disabled class="mask mask-star-2 {{ $i <= $ratingsStats['avg'] ? 'bg-orange-400' : 'bg-base-300' }}" />
                                @endfor
                            </div>
                            <span class="text-xs text-base-content/60 font-bold font-mono">{{ $ratingsStats['avg'] }} من 5</span>
                            <span class="text-xs text-base-content/40">|</span>
                            <span class="text-xs text-base-content/60 font-bold font-sans">({{ $ratingsStats['count'] }} تقييم)</span>
                        </div>

                        <!-- Price -->
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-3xl font-black text-secondary font-mono">{{ number_format($product->price, 2) }} ر.س</span>
                            <span class="text-xs text-base-content/50">شامل ضريبة القيمة المضافة</span>
                        </div>

                        <!-- Short Description -->
                        <p class="text-sm text-base-content/70 leading-relaxed mb-6">
                            {{ Str::limit(strip_tags($product->description), 160) }}
                        </p>

                        <!-- Variants Selector (Dummy Demonstration) -->
                        <div class="space-y-4 mb-6">
                            <!-- Colors -->
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-base-content/60 min-w-[50px]">الألوان:</span>
                                <div class="flex gap-2">
                                    @foreach(['أسود' => 'bg-black', 'فضي' => 'bg-slate-300', 'أزرق' => 'bg-blue-600'] as $name => $colorClass)
                                        <button wire:click="selectColor('{{ $name }}')" 
                                                class="w-6 h-6 rounded-full {{ $colorClass }} border-2 {{ $selectedColor === $name ? 'border-primary ring-2 ring-primary/20 scale-110' : 'border-transparent' }}"
                                                title="{{ $name }}"></button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Sizes -->
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-base-content/60 min-w-[50px]">المقاس:</span>
                                <div class="flex gap-2">
                                    @foreach(['S', 'M', 'L', 'XL'] as $size)
                                        <button wire:click="selectSize('{{ $size }}')" 
                                                class="btn btn-xs rounded-lg {{ $selectedSize === $size ? 'btn-neutral' : 'btn-outline border-base-300' }} px-3 font-mono font-bold">
                                            {{ $size }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quantifier and Add to Cart Button -->
                    <div class="flex items-center gap-4 pt-4 border-t border-base-200">
                        @if($product->stock > 0)
                            <div class="join border border-base-300 rounded-xl">
                                <button wire:click="decrementQuantity" class="btn btn-ghost join-item btn-sm px-3">-</button>
                                <span class="px-5 bg-base-100 font-bold font-mono text-sm join-item flex items-center">{{ $quantity }}</span>
                                <button wire:click="incrementQuantity" class="btn btn-ghost join-item btn-sm px-3">+</button>
                            </div>

                            <button wire:click="addToCart" class="btn btn-primary flex-1 rounded-xl font-bold shadow-lg hover:shadow-primary/30 gap-2">
                                إضافة إلى السلة
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        @else
                            <button disabled class="btn btn-block rounded-xl btn-neutral">المنتج غير متوفر حالياً</button>
                        @endif
                    </div>
                </div>

            </div>

            <!-- TABS FOR DETAILS & REVIEWS -->
            <div class="border-t border-base-200 mt-6" x-data="{ activeTab: 'description' }">
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

                <!-- DESCRIPTION TAB CONTENT -->
                <div x-show="activeTab === 'description'" class="p-6 pt-2 text-sm text-base-content/80 leading-relaxed text-right space-y-4">
                    <p>{{ $product->description }}</p>
                    <div class="border-t border-base-200 pt-4 mt-4">
                        <h4 class="font-bold text-base-content mb-2">المواصفات الأساسية:</h4>
                        <table class="table table-xs bg-base-200/40 rounded-xl overflow-hidden w-full">
                            <tbody>
                                <tr>
                                    <td class="font-bold text-base-content/60 w-1/3">القسم الرئيسي</td>
                                    <td>{{ $product->category ? $product->category->name : 'غير مصنف' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold text-base-content/60">حالة التوفر</td>
                                    <td>{{ $product->stock > 0 ? 'متوفر في المخزون' : 'نفدت الكمية' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold text-base-content/60">تاريخ الإضافة</td>
                                    <td>{{ $product->created_at->format('Y/m/d') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- REVIEWS TAB CONTENT -->
                <div x-show="activeTab === 'reviews'" class="p-6 pt-2 space-y-6">
                    
                    <!-- Ratings Breakdown stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center bg-base-200/40 p-5 rounded-2xl border border-base-200">
                        <!-- Score column -->
                        <div class="text-center md:border-l md:border-base-300">
                            <span class="text-5xl font-black text-base-content font-mono">{{ $ratingsStats['avg'] }}</span>
                            <p class="text-xs text-base-content/50 font-bold mt-1">من أصل 5 نجوم</p>
                            <div class="rating rating-sm justify-center mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <input type="radio" disabled class="mask mask-star-2 {{ $i <= $ratingsStats['avg'] ? 'bg-orange-400' : 'bg-base-300' }}" />
                                @endfor
                            </div>
                        </div>

                        <!-- Progress bars column -->
                        <div class="md:col-span-2 space-y-2">
                            @foreach([5, 4, 3, 2, 1] as $star)
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="font-bold text-base-content/70 w-10 text-left font-mono">{{ $star }} نجوم</span>
                                    <progress class="progress progress-warning flex-1" value="{{ $ratingsStats['percents'][$star] }}" max="100"></progress>
                                    <span class="font-bold text-base-content/60 w-8 font-mono">{{ $ratingsStats['percents'][$star] }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Create review form -->
                    <div class="bg-base-100 p-5 rounded-2xl border border-base-200 space-y-4">
                        <h4 class="font-bold text-base text-base-content">إضافة مراجعة وتقييم للمنتج</h4>
                        
                        <div class="space-y-3">
                            <!-- Star interactive rating selector -->
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-base-content/60">تقييمك بالنجوم:</span>
                                <div class="rating rating-md">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" name="rating-star-selector" wire:model="commentStar" value="{{ $i }}" class="mask mask-star-2 bg-orange-400" />
                                    @endfor
                                </div>
                            </div>

                            <!-- Comment body textarea -->
                            <div class="form-control">
                                <textarea wire:model="commentBody" placeholder="اكتب مراجعتك هنا وتحدث عن تجربتك مع المنتج..." class="textarea textarea-bordered rounded-xl h-24 text-sm w-full leading-relaxed"></textarea>
                            </div>

                            <!-- Submit button -->
                            <div class="flex justify-end">
                                <button wire:click="submitComment" class="btn btn-neutral btn-sm rounded-lg px-6 font-bold gap-2">
                                    <span wire:loading wire:target="submitComment" class="loading loading-spinner loading-xs"></span>
                                    إرسال المراجعة
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="space-y-4">
                        <h4 class="font-bold text-base text-base-content border-b border-base-200 pb-2">آراء وتجارب العملاء</h4>
                        
                        @if($product->comments->isEmpty())
                            <p class="text-sm text-base-content/50 text-center py-6">لا توجد تعليقات أو مراجعات لهذا المنتج بعد. كن أول من يضيف تقييمه!</p>
                        @else
                            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1">
                                @foreach($product->comments as $comment)
                                    <div class="flex gap-3 bg-base-200/30 p-4 rounded-xl border border-base-200/50">
                                        <!-- User Initials Avatar -->
                                        <div class="avatar placeholder flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-neutral text-neutral-content font-bold flex items-center justify-center text-xs">
                                                <span>{{ $comment->user ? $comment->user->initials() : 'ع' }}</span>
                                            </div>
                                        </div>

                                        <!-- Review Details -->
                                        <div class="flex-1 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h5 class="font-bold text-sm text-base-content">{{ $comment->user ? $comment->user->name : 'عميل سوق' }}</h5>
                                                <span class="text-[10px] text-base-content/40 font-bold font-mono">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>

                                            <!-- Stars for this review -->
                                            <div class="rating rating-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" disabled class="mask mask-star-2 {{ $i <= $comment->star ? 'bg-orange-400' : 'bg-base-300' }}" />
                                                @endfor
                                            </div>

                                            <!-- Body -->
                                            @if($comment->body)
                                                <p class="text-xs text-base-content/80 leading-relaxed mt-2 text-right">
                                                    {{ $comment->body }}
                                                </p>
                                            @else
                                                <p class="text-xs text-base-content/30 italic mt-2 text-right">قيم هذا المنتج بالنجوم دون كتابة تعليق.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal Action/Footer -->
            <div class="modal-action p-6 border-t border-base-200 bg-base-200/20">
                <button wire:click="closeModal" class="btn btn-neutral rounded-xl btn-sm font-bold px-6">إغلاق</button>
            </div>
        @endif

    </div>
    
    <!-- Modal Backdrop overlay -->
    <div class="modal-backdrop bg-neutral-focus/60 backdrop-blur-sm" wire:click="closeModal"></div>
</div>
