<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\CanResetPassword;

class ResetPasswordController extends Controller
{
    use CanResetPassword;

    protected $redirectTo = '/admin';  // Redirige a OpenAdmin después de resetear

    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);  // Vista para resetear la contraseña
    }
}
