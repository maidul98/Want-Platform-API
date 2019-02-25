<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class notifyFulfiller extends Notification
{
    use Queueable;

    public $fulfiller, $message, $user, $want;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $fulfiller, $message, $want)
    {
        $fulfiller = $this->fulfiller;
        $user = $this->user;
        $message = $this->message;
        $want = $this->want;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toDatabase($notifiable)
    {
        return [
            'sent_from' => $this->user,
            'message'=> $this->message,
            'sent_to'=> $this->fulfiller,
            'want' => $this->want,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return [
            'sent_from' => $this->user,
            'message'=> $this->message,
            'sent_to'=> $this->fulfiller,
            'want' => $this->want,
        ];
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
