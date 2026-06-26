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
        @endif
    </div>
</x-app-layout>
