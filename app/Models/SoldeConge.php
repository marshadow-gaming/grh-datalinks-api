<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoldeConge extends Model
{
    protected $table = 'soldes_conges';

    protected $fillable = [
        'employe_id', 'type_conge_id', 'annee',
        'jours_acquis', 'jours_pris'
    ];

    public function employe() {
        return $this->belongsTo(Employe::class);
    }

    public function typeConge() {
        return $this->belongsTo(TypeConge::class, 'type_conge_id');
    }
}