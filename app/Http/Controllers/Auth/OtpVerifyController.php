<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OtpVerifyController extends Controller
{

    public function show()
    {
        $phone = session('login_phone');

        if (!$phone) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify', compact('phone'));
    }
    public function store(Request $request)
    {
        $code = $request->validate([
            'code' => ['required']
        ]);


        $phone = session('login_phone');

        if (!$phone) {
            return redirect()->route('login');
        }


        $user = User::where('phone', $phone)->first();

        if ($user && $user->otp_code === $code['code'] && now()->isBefore($user->otp_expires_at)) {
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            Auth::login($user, true);

            session()->forget('login_phone');

            if ($user->name == 'user') {
                return redirect()->route('name');
            }

            return redirect()->route('dashboard');
        }
        dd('3874');

        return redirect()->route('login');
    }
}
