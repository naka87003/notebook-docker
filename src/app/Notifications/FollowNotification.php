<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class FollowNotification extends Notification
{
    use Queueable;
    protected $follower;

    /**
     * Create a new notification instance.
     */
    public function __construct($follower)
    {
        $this->follower = $follower;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = config('app.url') . '/timeline?user=' . $this->follower->id;

        $message = (new MailMessage)
            ->subject(Lang::get('You have a new follower!'))
            ->line(Lang::get($this->follower->name . ' followed you.'))
            ->line(Lang::get("Please click the button below to check your new follower's posts."))
            ->action(Lang::get('Check Posts'), $url);

        $message->viewData['data'] = [
            'image_path' => $this->follower->image_path
        ];

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'follower' => $this->follower,
        ];
    }
}
