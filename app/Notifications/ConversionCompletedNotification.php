<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\AudioFile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConversionCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly AudioFile $audioFile, private readonly int $fileCount) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('converter.split_notification_title'),
            'message' => $this->fileCount > 1
                ? __('converter.split_notification_multi', ['count' => $this->fileCount])
                : __('converter.split_notification_single'),
            'audio_file_id' => $this->audioFile->id,
            'file_count' => $this->fileCount,
        ];
    }
}
