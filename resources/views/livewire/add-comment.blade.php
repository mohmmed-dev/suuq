<div class="bg-base-100 p-5 rounded-2xl border border-base-200 space-y-4">
    <h4 class="font-bold text-base text-base-content">إضافة مراجعة وتقييم للمنتج</h4>

    @if($submitted)
        <div class="alert alert-success rounded-xl text-sm font-bold">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>تم إرسال تقييمك بنجاح! شكراً لمشاركتك.</span>
        </div>
        <button wire:click="$set('submitted', false)" class="btn btn-ghost btn-sm rounded-lg">إضافة تقييم آخر</button>
    @else
        <div class="space-y-4">
            {{-- Star selector --}}
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-base-content/60">تقييمك:</span>
                <div class="rating rating-md" role="radiogroup" aria-label="اختر عدد النجوم">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio"
                               name="star-{{ $productId }}"
                               wire:model="star"
                               value="{{ $i }}"
                               class="mask mask-star-2 bg-orange-400"
                               aria-label="{{ $i }} نجوم" />
                    @endfor
                </div>
                <span class="text-xs text-base-content/50 font-mono font-bold">{{ $star }}/5</span>
            </div>

            @error('star')
                <p class="text-error text-xs font-semibold">{{ $message }}</p>
            @enderror

            {{-- Comment textarea --}}
            <div class="form-control">
                <textarea wire:model="body"
                          placeholder="اكتب مراجعتك هنا وتحدث عن تجربتك مع المنتج..."
                          class="textarea textarea-bordered rounded-xl h-24 text-sm w-full leading-relaxed resize-none focus:outline-none focus:border-primary"
                          maxlength="500"
                          aria-label="نص المراجعة"></textarea>
                <div class="label">
                    <span class="label-text-alt text-base-content/40">{{ strlen($body) }}/500</span>
                </div>
            </div>

            @error('body')
                <p class="text-error text-xs font-semibold">{{ $message }}</p>
            @enderror

            {{-- Submit --}}
            <div class="flex justify-end">
                <button wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-wait"
                        class="btn btn-neutral btn-sm rounded-lg px-6 font-bold gap-2"
                        aria-label="إرسال المراجعة">
                    <span wire:loading wire:target="submit" class="loading loading-spinner loading-xs"></span>
                    إرسال المراجعة
                </button>
            </div>
        </div>
    @endif
</div>
