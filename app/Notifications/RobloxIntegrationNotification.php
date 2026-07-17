<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RobloxIntegrationNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['message' => $this->message];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Roblox Integration')->line($this->message);
    }
}
