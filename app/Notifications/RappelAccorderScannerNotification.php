<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RappelAccorderScannerNotification extends Notification
{
    use Queueable;

    protected $nomUtilisateur;

    public function __construct(string $nomUtilisateur)
    {
        $this->nomUtilisateur = $nomUtilisateur;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "N'oubliez pas d'accorder l'accès au scanner à {$this->nomUtilisateur} si cette personne doit pointer sa présence (via le QR individuel ou le QR général).",
        ];
    }
}