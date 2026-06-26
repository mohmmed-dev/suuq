<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public int $cartCount = 0;

    #[On('cart-updated')]
    public function updateCartCount(): void
    {
        $user = $this->getActiveUser();
        $this->cartCount = $user
            ? Cart::where('user_id', $user->id)->sum('quantity')
            : 0;
    }

    public function mount(): void
    {
        $this->updateCartCount();
    }

    public function openCart(): void
    {
        $this->dispatch('open-cart');
    }

    private function getActiveUser(): ?User
    {
        return auth()->user()
            ?? User::where('phone', '+966500000000')->first()
            ?? User::first();
    }

    public function render()
    {
        $user = $this->getActiveUser();
        return view('livewire.navbar', compact('user'));
    }
}
