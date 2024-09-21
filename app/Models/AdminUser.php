<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;

class AdminUser extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable, CanResetPasswordTrait;

    protected $table = 'admin_users';

    protected $fillable = [
        'username', // Aquí es donde se almacena el correo electrónico
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Obtiene el correo electrónico para el restablecimiento de contraseña.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->username; // Usa 'username' para el restablecimiento de contraseña
    }

    /**
     * Envía la notificación de restablecimiento de contraseña.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}



