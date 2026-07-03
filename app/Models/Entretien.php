<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    protected $fillable = [
        'candidature_id', 'date_entretien', 'lieu',
        'interviewer_id', 'compte_rendu'
    ];

    public function candidature() {
        return $this->belongsTo(Candidature::class);
    }

    public function interviewer() {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}