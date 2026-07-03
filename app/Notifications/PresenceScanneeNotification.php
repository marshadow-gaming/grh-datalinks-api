<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PresenceScanneeNotification extends Notification
{
    protected $type;
    protected $heure;

    public function __construct($type, $heure)
    {
        $this->type = $type;
        $this->heure = $heure;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $label = $this->type === 'arrivee' ? 'Arrivée' : 'Départ';

        return [
            'type'    => 'presence_scannee',
            'message' => "{$label} enregistrée à {$this->heure}",
            'heure'   => $this->heure,
        ];
    }
}