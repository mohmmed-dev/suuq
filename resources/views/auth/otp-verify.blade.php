<x-guest-layout>
    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <!-- Name -->
        <!-- Email Address -->
        <p>{{$phone}}</p>
        <div class="mt-4">
            <x-input-label for="otp" :value="__('Otp')" />
            <x-text-input id="otp" class="block mt-1 w-full" type='text' name="code" :value="old('otp')" required autocomplete="otp" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-4">
                {{ __('Next') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
