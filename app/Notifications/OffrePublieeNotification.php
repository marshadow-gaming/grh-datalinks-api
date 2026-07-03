<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;

class OffrePublieeNotification extends Notification
{
    protected $offre;

    public function __construct($offre)
    {
        $this->offre = $offre;
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'type'     => 'offre_publiee',
            'message'  => "Nouvelle offre d'emploi publiée : {$this->offre->titre} ({$this->offre->type_contrat})",
            'offre_id' => $this->offre->id,
        ];
    }
}