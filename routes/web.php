<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ===== PUBLIC MARKETPLACE ROUTES =====


Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'setting'])->name('setting');
    Route::get('/carts', [UserController::class, 'carts'])->name('carts');
    Route::get('/favorites', [UserController::class, 'favorites'])->name('favorites');
    Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
    Route::get('/orders', [UserController::class, 'setting'])->name('order.index');
    // Route::get('/order', [UserController::class, 'carts'])->name('order.store');
    Route::get('/order/{order}', [UserController::class, 'setting'])->name('order.show');
});

require __DIR__ . '/auth.php';
