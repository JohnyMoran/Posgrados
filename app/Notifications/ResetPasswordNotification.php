<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    protected $token;

    /**
     * Crear una nueva notificación de restablecimiento de contraseña.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Determina qué canales de notificación utilizar.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Construye el mensaje de correo electrónico de restablecimiento de contraseña.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //Log::info('Enviando correo con mailer:', ['mailer' => config('mail.default')]);
        return (new MailMessage)
                    ->subject('Restablecimiento de Contraseña')
                    ->line('Recibiste este correo porque se solicitó un restablecimiento de contraseña para tu cuenta.')
                    ->action('Restablecer Contraseña', url(config('app.url').route('password.reset', $this->token, false)))
                    ->line('Si no solicitaste un restablecimiento de contraseña, no es necesario hacer nada.');
    }

    /**
     * Obtiene la representación de la notificación en formato array.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
