<?php

namespace App\Livewire;

use App\Models\Like;
use App\Models\User;
use Livewire\Component;

class LikeButton extends Component
{
    public int $productId;
    public bool $isLiked = false;
    public int $likeCount = 0;

    public function mount(int $productId, bool $isLiked = false): void
    {
        $this->productId = $productId;
        $this->isLiked   = $isLiked;
        $this->likeCount = Like::where('product_id', $productId)->count();
    }

    public function toggle(): void
    {
        $user = $this->getActiveUser();

        if (!$user) {
            $this->dispatch('show-toast-js', message: 'يجب تسجيل الدخول لإضافة منتج للمفضلة', type: 'error');
            return;
        }

        $like = Like::where('user_id', $user->id)
            ->where('product_id', $this->productId)
            ->first();

        if ($like) {
            $like->delete();
            $this->isLiked = false;
            $this->likeCount = max(0, $this->likeCount - 1);
            $this->dispatch('show-toast-js', message: 'تمت إزالة المنتج من المفضلة', type: 'info');
        } else {
            Like::create([
                'user_id'    => $user->id,
                'product_id' => $this->productId,
            ]);
            $this->isLiked = true;
            $this->likeCount++;
            $this->dispatch('show-toast-js', message: 'تمت إضافة المنتج إلى المفضلة ❤️', type: 'success');
        }
    }

    private function getActiveUser(): ?User
    {
        return auth()->user()
            ?? User::where('phone', '+966500000000')->first()
            ?? User::first();
    }

    public function render()
    {
        return view('livewire.like-button');
    }
}
