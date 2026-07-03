<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffreEmploi extends Model
{
    protected $table = 'offres_emploi';

    protected $fillable = [
        'titre', 'departement_id', 'description', 'type_contrat',
        'statut', 'publiee_par', 'date_publication', 'date_limite'
    ];

    public function departement() {
        return $this->belongsTo(Departement::class);
    }

    public function publiePar() {
        return $this->belongsTo(User::class, 'publiee_par');
    }

    public function candidatures() {
        return $this->hasMany(Candidature::class, 'offre_id');
    }
}