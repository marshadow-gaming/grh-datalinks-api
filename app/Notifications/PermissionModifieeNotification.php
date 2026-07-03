<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;

class PermissionModifieeNotification extends Notification
{
    protected $module;
    protected $action;

    public function __construct($module, $action)
    {
        $this->module = $module;
        $this->action = $action; // 'accordee' ou 'retiree'
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        $moduleLabel = [
            'absences'             => 'Gestion des absences',
            'scanner'              => 'Scanner de présence',
            'historique_presence'  => 'Historique des présences',
            'travaux_stagiaire'    => 'Travaux stagiaires',
            'recrutement'          => 'Recrutement',
        ];

        $label = $moduleLabel[$this->module] ?? $this->module;
        $msg = $this->action === 'accordee'
            ? "Nouvelle permission accordée : accès au module \"{$label}\""
            : "Permission retirée : accès au module \"{$label}\" révoqué";

        return [
            'type'    => 'permission_modifiee',
            'message' => $msg,
            'module'  => $this->module,
            'action'  => $this->action,
        ];
    }
}