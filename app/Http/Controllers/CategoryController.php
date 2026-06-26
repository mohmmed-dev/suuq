<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Like;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function show(Request $request, Category $category)
    {
        // Fetch parent categories with their subcategories for the sidebar
        $categories = Category::whereNull('parent_id')
            ->with('subcategory')
            ->get();

        // Build the product query
        $query = $category->products()
            ->where('is_active', true)
            ->where('is_deleted', false);

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
        return view('categories.show',  compact(
            'products',
            'categories',
            'likedProductIds',
            'search',
            'category',
            'sortBy'
        ));
    }
}
