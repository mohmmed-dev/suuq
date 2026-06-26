<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    protected User $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function setting()
    {
        $user = $this->user;
        return view('profile.edit', compact('user'));
    }

    public function carts()
    {
        $user = $this->user;
        $products = $user->products()->paginate(12);

        return view('profile.carts', compact('products'));
    }

    public function favorites()
    {
        $user = $this->user;
        $products = $user->likes()->paginate(12);
        return view('profile.favorites', compact('products'));
    }
}
