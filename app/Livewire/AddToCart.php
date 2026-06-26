<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Livewire\Component;

class AddToCart extends Component
{
    public int $productId;
    public int $quantity = 1;
    public int $maxStock = 1;
    public bool $compact = false; // compact = card button only, full = stepper + button

    public function mount(int $productId, int $stock = 1, bool $compact = false): void
    {
        $this->productId = $productId;
        $this->maxStock  = $stock;
        $this->compact   = $compact;
    }

    public function increment(): void
    {
        if ($this->quantity < $this->maxStock) {
            $this->quantity++;
        }
    }

    public function decrement(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $user = $this->getActiveUser();

        if (!$user) {
            $this->dispatch('show-toast-js', message: 'يجب تسجيل الدخول لإضافة منتج إلى السلة', type: 'error');
            return;
        }

        $product = Product::find($this->productId);
        if (!$product || $product->stock < $this->quantity) {
            $this->dispatch('show-toast-js', message: 'عذراً، الكمية المطلوبة غير متوفرة في المخزون', type: 'error');
            return;
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $this->productId)
            ->first();

        if ($cart) {
            $cart->update(['quantity' => $cart->quantity + $this->quantity]);
        } else {
            Cart::create([
                'user_id'    => $user->id,
                'product_id' => $this->productId,
                'quantity'   => $this->quantity,
            ]);
        }

        $this->dispatch('cart-updated');
        $this->dispatch('show-toast-js', message: "تمت إضافة \"{$product->name}\" إلى السلة!", type: 'success');
    }

    private function getActiveUser(): ?User
    {
        return auth()->user()
            ?? User::where('phone', '+966500000000')->first()
            ?? User::first();
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
