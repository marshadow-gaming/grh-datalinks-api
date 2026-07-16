<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;

class NouvelleCandidatureNotification extends Notification
{
    protected $candidature;

    public function __construct($candidature)
    {
        $this->candidature = $candidature;
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'type'           => 'nouvelle_candidature',
            'message'        => "Nouvelle candidature reçue de {$this->candidature->nom_candidat} pour l'offre \"{$this->candidature->offre->titre}\"",
            'candidature_id' => $this->candidature->id,
        ];
    }
}