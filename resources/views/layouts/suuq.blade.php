<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'سوق | SUUQ' }}</title>
    <meta name="description" content="{{ $description ?? 'سوق - تسوّق بذكاء، اكتشف آلاف المنتجات بأسعار لا تُقارن' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Cairo', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-base-200 antialiased min-h-screen">

    {{-- Global Toast Notification --}}
    <div
        x-data="{
            toastMessage: '',
            toastType: 'success',
            showToast: false,
            triggerToast(msg, type) {
                this.toastMessage = msg;
                this.toastType = type;
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3500);
            }
        }"
        x-on:show-toast-js.window="triggerToast($event.detail.message, $event.detail.type)"
        class="toast toast-top toast-start z-[9999]"
    >
        <div
            x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display:none"
            class="alert rounded-xl shadow-xl border text-white font-bold text-sm"
            :class="{
                'alert-success bg-success border-success': toastType === 'success',
                'alert-error bg-error border-error': toastType === 'error',
                'alert-info bg-info border-info': toastType === 'info'
            }"
        >
            <span x-text="toastMessage"></span>
        </div>
    </div>

    {{-- Livewire: Navbar (Island - handles cart count + search dispatch) --}}
    <div class="sticky top-0 z-50 px-4 pt-3 pb-1">
        @livewire('navbar')
    </div>

    {{-- Main page content --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    {{-- Livewire: Cart Drawer (Island) --}}
    @livewire('cart-drawer')

    @stack('scripts')
</body>
</html>
