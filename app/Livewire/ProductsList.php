<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Like;
use App\Models\User;

class ProductsList extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = null;
    public $minPrice = 0;
    public $maxPrice = 2000;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public $toastMessage = '';
    public $toastType = 'success'; // success, error, info

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 2000],
        'sortBy' => ['except' => 'created_at'],
    ];

    #[On('search-updated')]
    public function searchUpdated($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedCategory = null;
        $this->minPrice = 0;
        $this->maxPrice = 2000;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $user = $this->getActiveUser();
        if (!$user) {
            $this->showToast('يجب تسجيل الدخول لإضافة منتج إلى السلة', 'error');
            return;
        }

        $cart = Cart::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => 1,
            ]);
        }

        $this->dispatch('cart-updated');
        $productName = Product::find($productId)?->name;
        $this->showToast("تمت إضافة \"{$productName}\" إلى السلة بنجاح!", 'success');
    }

    public function toggleLike($productId)
    {
        $user = $this->getActiveUser();
        if (!$user) {
            $this->showToast('يجب تسجيل الدخول لإضافة منتج للمفضلة', 'error');
            return;
        }

        $like = Like::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($like) {
            $like->delete();
            $this->showToast('تمت إزالة المنتج من المفضلة', 'info');
        } else {
            Like::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $this->showToast('تمت إضافة المنتج إلى المفضلة', 'success');
        }
    }

    public function viewDetails($productId)
    {
        $this->dispatch('view-product', productId: $productId);
    }

    public function showToast($message, $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
        $this->dispatch('show-toast-js', message: $message, type: $type);
    }

    private function getActiveUser()
    {
        return auth()->user() ?? User::where('phone', '+966500000000')->first() ?? User::first();
    }

    public function render()
    {
        $user = $this->getActiveUser();

        // 1. Fetch categories
        $categories = Category::whereNull('parent_id')
            ->with('subcategory')
            ->get();

        // 2. Fetch products query with filters
        $productsQuery = Product::query()
            ->where('is_active', true)
            ->where('is_deleted', false);

        if (!empty($this->search)) {
            $productsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedCategory) {
            // Find if category is parent or child
            $categoryIds = Category::where('id', $this->selectedCategory)
                ->orWhere('parent_id', $this->selectedCategory)
                ->pluck('id')
                ->toArray();
            
            $productsQuery->whereIn('category_id', $categoryIds);
        }

        $productsQuery->whereBetween('price', [$this->minPrice, $this->maxPrice]);

        // Sorting
        if ($this->sortBy === 'price_asc') {
            $productsQuery->orderBy('price', 'asc');
        } elseif ($this->sortBy === 'price_desc') {
            $productsQuery->orderBy('price', 'desc');
        } elseif ($this->sortBy === 'name') {
            $productsQuery->orderBy('name', 'asc');
        } else {
            $productsQuery->orderBy('created_at', 'desc');
        }

        $products = $productsQuery->with('category', 'comments')->paginate(12);

        // Fetch likes IDs of the active user for fast checking
        $likedProductIds = [];
        if ($user) {
            $likedProductIds = Like::where('user_id', $user->id)
                ->pluck('product_id')
                ->toArray();
        }

        return view('livewire.products-list', [
            'categories' => $categories,
            'products' => $products,
            'likedProductIds' => $likedProductIds,
        ]);
    }
}
