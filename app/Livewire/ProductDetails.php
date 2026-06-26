<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Cart;
use App\Models\User;

class ProductDetails extends Component
{
    public $isOpen = false;
    public $productId = null;
    public $quantity = 1;
    public $selectedColor = '';
    public $selectedSize = '';

    // Reviews (Comments) fields
    public $commentBody = '';
    public $commentStar = 5;

    // Toast message for details modal
    public $modalToast = '';
    public $modalToastType = 'success';

    #[On('view-product')]
    public function showProduct($productId)
    {
        $this->productId = $productId;
        $this->quantity = 1;
        $this->selectedColor = '';
        $this->selectedSize = '';
        $this->commentBody = '';
        $this->commentStar = 5;
        $this->modalToast = '';
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function selectColor($color)
    {
        $this->selectedColor = $color;
    }

    public function selectSize($size)
    {
        $this->selectedSize = $size;
    }

    public function incrementQuantity()
    {
        $product = Product::find($this->productId);
        if ($product && $this->quantity < $product->stock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $user = $this->getActiveUser();
        if (!$user) {
            $this->showModalToast('يجب تسجيل الدخول لإضافة منتج إلى السلة', 'error');
            return;
        }

        $product = Product::find($this->productId);
        if (!$product) {
            $this->showModalToast('المنتج غير موجود', 'error');
            return;
        }

        // Check stock
        if ($product->stock < $this->quantity) {
            $this->showModalToast('عذراً، الكمية المطلوبة غير متوفرة في المخزون', 'error');
            return;
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $this->productId)
            ->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + $this->quantity
            ]);
        } else {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $this->productId,
                'quantity' => $this->quantity,
            ]);
        }

        $this->dispatch('cart-updated');
        $this->showModalToast('تمت إضافة المنتج إلى السلة بنجاح!', 'success');
    }

    public function submitComment()
    {
        $user = $this->getActiveUser();
        if (!$user) {
            $this->showModalToast('يجب تسجيل الدخول لإضافة تعليق وتقييم', 'error');
            return;
        }

        $this->validate([
            'commentStar' => 'required|integer|between:1,5',
            'commentBody' => 'nullable|string|max:500',
        ]);

        Comment::create([
            'user_id' => $user->id,
            'product_id' => $this->productId,
            'body' => $this->commentBody,
            'star' => $this->commentStar,
        ]);

        $this->commentBody = '';
        $this->commentStar = 5;
        $this->showModalToast('تمت إضافة تقييمك بنجاح! شكراً لك.', 'success');
    }

    public function showModalToast($message, $type = 'success')
    {
        $this->modalToast = $message;
        $this->modalToastType = $type;
        $this->dispatch('show-toast-js', message: $message, type: $type);
    }

    private function getActiveUser()
    {
        return auth()->user() ?? User::where('phone', '+966500000000')->first() ?? User::first();
    }

    public function render()
    {
        $product = null;
        $ratingsStats = [
            'avg' => 0,
            'count' => 0,
            'stars' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
            'percents' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
        ];

        if ($this->productId) {
            $product = Product::with(['category', 'comments.user'])->find($this->productId);
            
            if ($product) {
                $comments = $product->comments;
                $ratingsStats['count'] = $comments->count();
                $ratingsStats['avg'] = round($comments->avg('star'), 1);

                if ($ratingsStats['count'] > 0) {
                    foreach ($comments as $comment) {
                        $star = (int) $comment->star;
                        if (isset($ratingsStats['stars'][$star])) {
                            $ratingsStats['stars'][$star]++;
                        }
                    }

                    foreach ($ratingsStats['stars'] as $starKey => $count) {
                        $ratingsStats['percents'][$starKey] = round(($count / $ratingsStats['count']) * 100);
                    }
                }
            }
        }

        return view('livewire.product-details', [
            'product' => $product,
            'ratingsStats' => $ratingsStats,
        ]);
    }
}
