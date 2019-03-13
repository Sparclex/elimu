<?php

namespace App\Notifications;

use NotificationChannels\PusherPushNotifications\Message;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessful as BaseNotification;

class BackupWasSuccessful extends BaseNotification
{
    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.user_id'))
            ->content("The backup of {$this->applicationName()} to disk {$this->diskName()} was successful");
    }
}
