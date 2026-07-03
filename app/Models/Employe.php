<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employe extends Model
{
    protected $fillable = [
        'user_id', 'departement_id', 'matricule', 'qr_token', 'poste',
        'type_contrat', 'date_embauche', 'date_fin_contrat',
        'date_naissance', 'adresse', 'salaire', 'jours_conge_annuels', 'notes'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($employe) {
            $employe->qr_token = Str::uuid();
        });
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function departement() {
        return $this->belongsTo(Departement::class);
    }

    public function documents() {
        return $this->hasMany(DocumentEmploye::class);
    }

    public function demandesAbsence() {
        return $this->hasMany(DemandeAbsence::class);
    }

    public function soldesConges() {
        return $this->hasMany(SoldeConge::class);
    }

    public function presences() {
        return $this->hasMany(Presence::class);
    }
    public function travaux() {
        return $this->hasMany(TravailStagiaire::class);
    }
}