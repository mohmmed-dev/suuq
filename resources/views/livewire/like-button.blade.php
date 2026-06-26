<button wire:click="toggle"
        wire:loading.attr="disabled"
        class="btn btn-circle btn-sm bg-base-100/80 hover:bg-base-100 border-none shadow-md transition-all duration-200 hover:scale-110"
        :class="{ 'scale-110': isLiked }"
        aria-label="{{ $isLiked ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}"
        title="{{ $isLiked ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}">

    <svg xmlns="http://www.w3.org/2000/svg"
         class="h-5 w-5 transition-all duration-300 {{ $isLiked ? 'fill-red-500 text-red-500 scale-110' : 'text-base-content/40 hover:text-red-400' }}"
         fill="{{ $isLiked ? 'currentColor' : 'none' }}"
         viewBox="0 0 24 24"
         stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
</button>
