<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\Cart;

class CartDrawer extends Component
{
    public $isOpen = false;
    public $cartItems = [];
    public $subtotal = 0;
    public $discount = 0;
    public $couponCode = '';
    public $couponError = '';
    public $couponSuccess = '';

    #[On('open-cart')]
    public function openCart()
    {
        $this->isOpen = true;
        $this->loadCart();
    }

    #[On('cart-updated')]
    public function loadCart()
    {
        $user = $this->getActiveUser();
        if ($user) {
            $this->cartItems = Cart::where('user_id', $user->id)
                ->with('product')
                ->get()
                ->toArray();
            
            $this->calculateTotals();
        } else {
            $this->cartItems = [];
            $this->subtotal = 0;
            $this->discount = 0;
        }
    }

    public function closeCart()
    {
        $this->isOpen = false;
    }

    public function incrementQuantity($cartId)
    {
        $cart = Cart::find($cartId);
        if ($cart) {
            $cart->increment('quantity');
            $this->dispatch('cart-updated');
        }
    }

    public function decrementQuantity($cartId)
    {
        $cart = Cart::find($cartId);
        if ($cart) {
            if ($cart->quantity > 1) {
                $cart->decrement('quantity');
            } else {
                $cart->delete();
            }
            $this->dispatch('cart-updated');
        }
    }

    public function removeItem($cartId)
    {
        $cart = Cart::find($cartId);
        if ($cart) {
            $cart->delete();
            $this->dispatch('cart-updated');
        }
    }

    public function applyCoupon()
    {
        if (trim($this->couponCode) === 'SUUQ10') {
            $this->couponSuccess = 'تمت إضافة كوبون الخصم بنجاح! تم خصم 10%';
            $this->couponError = '';
            $this->calculateTotals();
        } else {
            $this->couponError = 'رمز الكوبون غير صحيح. جرب "SUUQ10".';
            $this->couponSuccess = '';
            $this->discount = 0;
        }
    }

    private function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cartItems as $item) {
            if (isset($item['product'])) {
                $this->subtotal += $item['quantity'] * $item['product']['price'];
            }
        }

        if ($this->couponSuccess) {
            $this->discount = $this->subtotal * 0.10; // 10% discount
        } else {
            $this->discount = 0;
        }
    }

    private function getActiveUser()
    {
        return auth()->user() ?? User::where('phone', '+966500000000')->first() ?? User::first();
    }

    public function render()
    {
        return view('livewire.cart-drawer');
    }
}
