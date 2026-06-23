<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NameController extends Controller
{

    public function show()
    {
        $user = auth()->user();
        return view('auth.name', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $user = auth()->user();
        $user->update([
            'name' => $request->name,
        ]);
        return redirect()->route('dashboard');
    }
}
