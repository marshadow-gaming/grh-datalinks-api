<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class QrGlobalHorsDelaiNotification extends Notification
{
    use Queueable;

    protected $heureLimite;

    public function __construct(string $heureLimite)
    {
        $this->heureLimite = $heureLimite;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "L'heure limite ({$this->heureLimite}) pour pointer votre arrivée via le QR Code général est dépassée. Merci de vous enregistrer auprès de la DRH avec votre QR Code personnel.",
        ];
    }
}