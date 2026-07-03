<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DemandeTraiteeNotification extends Notification
{
    protected $demande;

    public function __construct($demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $statutLabel = $this->demande->statut === 'approuvee' ? 'approuvée' : 'rejetée';
        $typeLabel = $this->demande->type === 'conge' ? 'congé' : 'permission';

        return [
            'type'       => 'demande_traitee',
            'message'    => "Votre demande de {$typeLabel} a été {$statutLabel}",
            'demande_id' => $this->demande->id,
            'statut'     => $this->demande->statut,
        ];
    }
}