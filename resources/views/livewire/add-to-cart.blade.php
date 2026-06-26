<div>
    @if($maxStock > 0)
        @if($compact)
            {{-- Compact mode: single button for product cards --}}
            <button wire:click="addToCart"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-70 cursor-wait"
                    class="btn btn-primary btn-sm rounded-lg font-bold gap-1 shadow-sm hover:shadow-primary/30 transition-all"
                    aria-label="إضافة إلى السلة">
                <span wire:loading wire:target="addToCart" class="loading loading-spinner loading-xs"></span>
                <span wire:loading.remove wire:target="addToCart">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
                السلة
            </button>
        @else
            {{-- Full mode: quantity stepper + button for detail page --}}
            <div class="flex items-center gap-3">
                <div class="join border border-base-300 rounded-xl">
                    <button wire:click="decrement"
                            class="btn btn-ghost join-item btn-sm px-3 font-bold text-base"
                            aria-label="تقليل الكمية">−</button>
                    <span class="px-5 bg-base-100 font-bold font-mono text-sm join-item flex items-center min-w-[2.5rem] justify-center">
                        {{ $quantity }}
                    </span>
                    <button wire:click="increment"
                            class="btn btn-ghost join-item btn-sm px-3 font-bold text-base"
                            aria-label="زيادة الكمية">+</button>
                </div>

                <button wire:click="addToCart"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-wait"
                        class="btn btn-primary flex-1 rounded-xl font-bold shadow-lg hover:shadow-primary/30 gap-2 transition-all"
                        aria-label="إضافة إلى السلة">
                    <span wire:loading wire:target="addToCart" class="loading loading-spinner loading-sm"></span>
                    <span wire:loading.remove wire:target="addToCart">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                    إضافة إلى السلة
                </button>
            </div>
        @endif
    @else
        <button disabled class="btn rounded-xl btn-neutral opacity-60 btn-sm">نفدت الكمية</button>
    @endif
</div>
