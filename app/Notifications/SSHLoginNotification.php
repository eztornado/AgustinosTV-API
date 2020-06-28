<?php

namespace App\Notifications;

use App\SSHLoginLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SSHLoginNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $registro = SSHLoginLogs::find($this->id);

        return (new MailMessage)
                    ->from('ivanps94@gmail.com','TornadoCore')
                    ->subject('Nuevo Inicio de Sesión SSH en '.env('APP_URL'))
                    ->line('Se ha registrado un inicio de sesión SSH con el usuario : '.$registro->user." en la ip : ".$registro->ip." con timestamp : ".$registro->created_at);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
