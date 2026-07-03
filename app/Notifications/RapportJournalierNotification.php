<?php
namespace App\Notifications;
use Illuminate\Notifications\Notification;

class RapportJournalierNotification extends Notification
{
    protected $travail;

    public function __construct($travail)
    {
        $this->travail = $travail;
    }

    public function via($notifiable) { return ['database']; }

    public function toDatabase($notifiable)
    {
        return [
            'type'       => 'rapport_journalier',
            'message'    => "Nouveau rapport journalier soumis par {$this->travail->employe->user->name} : {$this->travail->titre}",
            'employe_id' => $this->travail->employe_id,
            'travail_id' => $this->travail->id,
        ];
    }
}