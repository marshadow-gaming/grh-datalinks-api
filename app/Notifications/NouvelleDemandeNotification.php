<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;

class NouvelleDemandeNotification extends Notification
{
    protected $demande;

    public function __construct($demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        $type = $this->demande->type === 'conge' ? 'congé' : 'permission';
        return [
            'type'       => 'nouvelle_demande',
            'message'    => "{$this->demande->employe->user->name} a soumis une demande de {$type}",
            'demande_id' => $this->demande->id,
        ];
    }
}