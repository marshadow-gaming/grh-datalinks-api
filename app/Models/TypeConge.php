<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeConge extends Model
{
    protected $table = 'types_conges';

    protected $fillable = ['nom', 'jours_par_an'];

    public function soldes() {
        return $this->hasMany(SoldeConge::class, 'type_conge_id');
    }
}