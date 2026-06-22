<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:10', 'unique:' . User::class],
        ]);

        $user = User::firstOrCreate([
            'phone' => $request->phone,
        ]);

        $user->update([
            'otp_code' => rand(1000, 9999),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new SendOtpNotification());
        session()->put('login_phone', $user->phone);

        return redirect(route('otp.verify', absolute: false));
    }
}
