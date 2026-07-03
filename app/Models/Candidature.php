<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = [
        'offre_id', 'nom_candidat', 'email_candidat', 'telephone_candidat',
        'cv_fichier', 'lettre_motivation', 'statut'
    ];

    public function offre() {
        return $this->belongsTo(OffreEmploi::class, 'offre_id');
    }

    public function entretiens() {
        return $this->hasMany(Entretien::class);
    }
}