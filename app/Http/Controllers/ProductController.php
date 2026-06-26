<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Product listing page with filters via GET parameters.
     */
    public function index(Request $request)
    {
        // Fetch parent categories with their subcategories for the sidebar
        $categories = Category::whereNull('parent_id')
            ->with('subcategory')
            ->get();

        // Build the product query
        $query = Product::query()
            ->where('is_active', true)
            ->where('is_deleted', false)
            ->with(['category', 'comments']);

        // --- Filters from GET params ---
        $search = $request->input('search', '');
        $sortBy = $request->input('sort', 'created_at');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        match ($sortBy) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        // Liked product IDs for the current user
        $likedProductIds = [];
        if (auth()->check()) {
            $likedProductIds = Like::where('user_id', auth()->id())
                ->pluck('product_id')
                ->toArray();
        }

        return view('products.index', compact(
            'products',
            'categories',
            'likedProductIds',
            'search',
            'sortBy'
        ));
    }

    /**
     * Product detail page.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_deleted', false)
            ->with(['category', 'comments.user'])
            ->firstOrFail();

        // Build ratings stats
        $comments = $product->comments;
        $count = $comments->count();
        $avg = $count > 0 ? round($comments->avg('star'), 1) : 0;

        $stars = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $percents = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        foreach ($comments as $comment) {
            $star = (int) $comment->star;
            if (isset($stars[$star])) {
                $stars[$star]++;
            }
        }

        if ($count > 0) {
            foreach ($stars as $k => $v) {
                $percents[$k] = round(($v / $count) * 100);
            }
        }

        $ratingsStats = compact('avg', 'count', 'stars', 'percents');

        // Related products (same category, excluding current)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('is_deleted', false)
            ->with(['category', 'comments'])
            ->limit(4)
            ->get();

        // Is liked by current user?
        $isLiked = false;
        if (auth()->check()) {
            $isLiked = Like::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
        }

        return view('products.show', compact('product', 'ratingsStats', 'relatedProducts', 'isLiked'));
    }
}
