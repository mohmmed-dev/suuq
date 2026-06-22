<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class OtpVerifyController extends Controller
{
    public function otpVerify(Request $request)
    {
        $code = $request->validate([
            'code' => ['required', 'numeric']
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

            return redirect()->route('dashboard');
        }

        return redirect()->route('login');
    }
}
