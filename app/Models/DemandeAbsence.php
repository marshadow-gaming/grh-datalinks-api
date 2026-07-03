<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeAbsence extends Model
{
    protected $table = 'demandes_absence';

    protected $fillable = [
        'employe_id', 'type', 'date_debut', 'date_fin',
        'heure_debut', 'heure_fin', 'motif',
        'statut', 'traite_par', 'commentaire'
    ];

    public function employe() {
        return $this->belongsTo(Employe::class);
    }

    public function traitePar() {
        return $this->belongsTo(User::class, 'traite_par');
    }
}