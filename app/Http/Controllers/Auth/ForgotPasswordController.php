<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'username' => 'required|string|email',
        ]);

        $status = Password::broker('admin_users')->sendResetLink($request->only('username'));

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }
    
        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}

