<div class="relative z-[100]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" x-data="{ open: @entangle('isOpen') }" x-show="open" style="display: none;">
    <!-- Background backdrop with blur -->
    <div class="fixed inset-0 bg-base-300/60 backdrop-blur-sm transition-opacity" 
         x-show="open" 
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         wire:click="closeCart"></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-full pl-10 pr-0">
                <!-- Slide-over panel -->
                <div class="pointer-events-auto w-screen max-w-md transform transition-all duration-300"
                     x-show="open"
                     x-transition:enter="transform transition ease-in-out duration-300 sm:duration-300"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300 sm:duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full">
                    
                    <div class="flex h-full flex-col overflow-y-scroll bg-base-100 shadow-2xl border-r border-base-200">
                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-base-200 px-6 py-5 bg-base-200/40">
                            <h2 class="text-lg font-bold text-base-content flex items-center gap-2" id="slide-over-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                سلة المشتريات
                            </h2>
                            <button type="button" wire:click="closeCart" class="btn btn-ghost btn-circle btn-sm text-base-content/60 hover:text-base-content">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Body (Cart items list) -->
                        <div class="flex-1 px-6 py-4 overflow-y-auto">
                            <!-- Skeleton loader while recalculating/updating -->
                            <div wire:loading class="w-full space-y-4">
                                @for($i = 0; $i < 3; $i++)
                                    <div class="flex items-center gap-4 border-b border-base-100 pb-4 animate-pulse">
                                        <div class="w-16 h-16 bg-base-300 rounded-lg"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="h-4 bg-base-300 rounded w-2/3"></div>
                                            <div class="h-3 bg-base-300 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div wire:loading.remove>
                                @if(empty($cartItems))
                                    <!-- Empty state -->
                                    <div class="flex flex-col items-center justify-center h-80 text-center">
                                        <div class="bg-base-200 p-4 rounded-full mb-4 text-base-content/40">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-base-content mb-1">عربتك فارغة حالياً</h3>
                                        <p class="text-sm text-base-content/60 mb-6">لم تقم بإضافة أي منتجات للسلة بعد.</p>
                                        <button wire:click="closeCart" class="btn btn-primary rounded-lg btn-sm px-6">تصفح المنتجات الآن</button>
                                    </div>
                                @else
                                    <!-- Items list -->
                                    <div class="space-y-4">
                                        @foreach($cartItems as $item)
                                            @if(isset($item['product']))
                                                <div class="flex items-center gap-4 border-b border-base-200 pb-4">
                                                    <!-- Product Cover Image -->
                                                    <div class="w-20 h-20 rounded-lg overflow-hidden border border-base-200 flex-shrink-0 bg-base-200">
                                                        @php
                                                            $covers = json_decode($item['product']['covers'], true);
                                                            $image = $covers[0] ?? 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=150&auto=format&fit=crop';
                                                        @endphp
                                                        <img src="{{ $image }}" alt="{{ $item['product']['name'] }}" class="w-full h-full object-cover" />
                                                    </div>

                                                    <!-- Product Info -->
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="font-bold text-sm text-base-content truncate">{{ $item['product']['name'] }}</h4>
                                                        <p class="text-xs text-base-content/60 mb-2">سعر القطعة: {{ number_format($item['product']['price'], 2) }} ر.س</p>
                                                        
                                                        <div class="flex items-center justify-between">
                                                            <!-- Quantity Controls -->
                                                            <div class="join border border-base-300 rounded-lg">
                                                                <button wire:click="decrementQuantity({{ $item['id'] }})" class="btn btn-ghost btn-xs join-item px-2">-</button>
                                                                <span class="px-3 py-1 bg-base-100 text-xs font-bold font-mono join-item flex items-center">{{ $item['quantity'] }}</span>
                                                                <button wire:click="incrementQuantity({{ $item['id'] }})" class="btn btn-ghost btn-xs join-item px-2">+</button>
                                                            </div>

                                                            <!-- Total Item Price -->
                                                            <span class="text-sm font-black text-secondary font-mono">{{ number_format($item['quantity'] * $item['product']['price'], 2) }} ر.س</span>
                                                        </div>
                                                    </div>

                                                    <!-- Delete button -->
                                                    <button wire:click="removeItem({{ $item['id'] }})" class="btn btn-ghost btn-circle btn-xs text-error hover:bg-error/10">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Footer (Totals & Coupon) -->
                        @if(!empty($cartItems))
                            <div class="border-t border-base-200 px-6 py-5 bg-base-200/30 space-y-4">
                                <!-- Coupon section -->
                                <div class="space-y-2">
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="couponCode" placeholder="رمز الكوبون (مثال: SUUQ10)" class="input input-bordered input-sm flex-1 rounded-lg text-xs" />
                                        <button wire:click="applyCoupon" class="btn btn-neutral btn-sm rounded-lg text-xs px-4">تطبيق</button>
                                    </div>
                                    @if($couponError)
                                        <p class="text-error text-xs font-semibold">{{ $couponError }}</p>
                                    @endif
                                    @if($couponSuccess)
                                        <p class="text-success text-xs font-semibold">{{ $couponSuccess }}</p>
                                    @endif
                                </div>

                                <!-- Financial summary -->
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between text-base-content/70">
                                        <span>المجموع الفرعي:</span>
                                        <span class="font-bold font-mono">{{ number_format($subtotal, 2) }} ر.س</span>
                                    </div>
                                    @if($discount > 0)
                                        <div class="flex justify-between text-success">
                                            <span>الخصم (10%):</span>
                                            <span class="font-bold font-mono">-{{ number_format($discount, 2) }} ر.س</span>
                                        </div>
                                    @endif
                                    
                                    @php
                                        $finalPrice = $subtotal - $discount;
                                        $shipping = ($finalPrice > 200 || $finalPrice <= 0) ? 0 : 15.00;
                                    @endphp
                                    <div class="flex justify-between text-base-content/70">
                                        <span>الشحن:</span>
                                        <span class="font-bold font-mono">
                                            @if($shipping == 0)
                                                <span class="badge badge-success badge-sm font-sans">شحن مجاني</span>
                                            @else
                                                {{ number_format($shipping, 2) }} ر.س
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if($shipping > 0)
                                        <p class="text-xs text-base-content/50">أضف بقيمة <span class="font-bold font-mono text-secondary">{{ number_format(200 - $finalPrice, 2) }} ر.س</span> إضافية للحصول على شحن مجاني!</p>
                                    @endif

                                    <div class="flex justify-between text-base font-black border-t border-base-300 pt-3 text-base-content">
                                        <span>المجموع الإجمالي:</span>
                                        <span class="font-black text-secondary text-lg font-mono">{{ number_format($finalPrice + $shipping, 2) }} ر.س</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="pt-2">
                                    <button class="btn btn-primary btn-block rounded-xl gap-2 font-bold shadow-lg hover:shadow-primary/30">
                                        إتمام عملية الشراء
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
