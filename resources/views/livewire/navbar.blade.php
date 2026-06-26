<div class="navbar bg-base-100 shadow-md rounded-xl px-4 py-2 border border-base-200">

    {{-- === Brand / Mobile Hamburger === --}}
    <div class="navbar-start gap-2">
        {{-- <div class="dropdown" x-data="{ open: true }">
            <div tabindex="0" role="button" @click="open = !open" class="btn btn-ghost lg:hidden" aria-label="القائمة">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                </svg>
            </div>
            <ul x-show="open" @click.away="open = false"
                class="menu menu-sm dropdown-content mt-3 z-[100] p-2 shadow bg-base-100 rounded-box w-52 border border-base-200">
                <li><a href="{{ route('products.index') }}" class="font-medium">الرئيسية</a></li>
                <li><a href="{{ route('products.index', ['sort' => 'created_at']) }}" class="font-medium">الأحدث</a></li>
                <li><a href="{{ route('products.index', ['sort' => 'price_asc']) }}" class="font-medium">الأسعار المنخفضة</a></li>
            </ul>
        </div> --}}

        <a href="{{ route('products.index') }}" class="btn btn-ghost text-xl font-black gap-1 text-primary normal-case">
            <span class="text-secondary font-sans">SUUQ</span>
            <span class="text-primary font-bold">سوق</span>
        </a>
    </div>

    {{-- === Desktop Nav Links === --}}
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1 gap-2 font-medium">
            <li><a href="{{ route('products.index') }}" class="rounded-lg hover:bg-primary/10 hover:text-primary">الرئيسية</a></li>
            <li><a href="{{ route('products.index', ['sort' => 'created_at']) }}" class="rounded-lg hover:bg-primary/10 hover:text-primary">الأحدث</a></li>
            <li><a href="{{ route('products.index', ['sort' => 'price_asc']) }}" class="rounded-lg hover:bg-primary/10 hover:text-primary">الأسعار المنخفضة</a></li>
        </ul>
    </div>

    {{-- === Search + Actions === --}}
    <div class="navbar-end gap-2">

        {{-- Desktop Search (GET form) --}}
        <form method="GET" action="{{ route('products.index') }}" class="form-control hidden md:block w-64 relative">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="ابحث عن منتجات..."
                   class="input input-bordered input-sm w-full rounded-lg pr-8"
                   aria-label="البحث عن منتجات" />
            <button type="submit" class="absolute left-2 top-2 text-base-content/50 hover:text-primary" aria-label="بحث">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </form>
        {{-- Cart Button (Livewire event) --}}
        <a href="{{ route('carts') }}" class="btn btn-ghost btn-circle relative"
                aria-label="عربة التسوق">
            <div class="indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 stroke-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @if($cartCount > 0)
                    <span class="badge badge-secondary badge-sm indicator-item font-bold animate-pulse">{{ $cartCount }}</span>
                @endif
            </div>
        </a>

        {{-- User Dropdown --}}
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button"
                 class="btn btn-ghost btn-circle avatar border-2 border-primary/20"
                 aria-label="قائمة المستخدم">
                <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold text-sm">
                    <span>{{ $user ? $user->initials() : 'ز' }}</span>
                </div>
            </div>
            <ul tabindex="0"
                class="menu menu-sm dropdown-content mt-3 z-[100] p-2 shadow bg-base-100 rounded-box w-52 border border-base-200">
                <div class="px-4 py-2 border-b border-base-200">
                    <p class="font-bold text-base-content">{{ $user ? $user->name : 'زائر' }}</p>
                    <p class="text-xs text-base-content/60">{{ $user ? $user->phone : '' }}</p>
                </div>
                <li><a href="{{ route('setting') }}" class="py-2 hover:text-primary font-medium">الملف الشخصي</a></li>
                <li><a href{{ route('order.index') }} class="py-2 hover:text-primary font-medium">طلباتي</a></li>
                <li><a href="{{ route('favorites') }}" class="py-2 hover:text-primary font-medium">المفضلة</a></li>
                <li class="border-t border-base-200 mt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="py-2 text-error hover:bg-error/10 w-full text-right font-medium rounded-md px-3">
                            تسجيل الخروج
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
