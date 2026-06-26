{{-- ===== HERO CATEGORY CAROUSEL ===== --}}
<div class="mb-8 overflow-x-auto pb-2 flex gap-3 scrollbar-hide">
    <a href="{{ route('products.index') }}"
        class="btn rounded-full btn-sm px-6 font-bold flex-shrink-0 {{ !request()->routeIs('category*') && !request('search') ? 'btn-primary' : 'btn-outline border-base-300 hover:border-primary' }}">
        الكل
    </a>
    @foreach($categories as $parentCat)
        <a href="{{ route('category.show',$parentCat->slug) }}"
            class="btn rounded-full btn-sm px-6 font-bold flex-shrink-0 {{ ($categorySlug ?? '') == $parentCat->slug ? 'btn-primary' : 'btn-outline border-base-300 hover:border-primary' }}">
            {{ $parentCat->name }}
        </a>
    @endforeach
</div>
