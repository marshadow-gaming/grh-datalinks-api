<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    protected $fillable = ['nom', 'description'];

    public function employes() {
        return $this->hasMany(Employe::class);
    }

    public function offresEmploi() {
        return $this->hasMany(OffreEmploi::class);
    }
}