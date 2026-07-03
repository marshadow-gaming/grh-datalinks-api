<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $fillable = [
        'employe_id', 'date', 'heure_arrivee', 'heure_depart', 'scanne_par'
    ];

    public function employe() {
        return $this->belongsTo(Employe::class);
    }

    public function scannePar() {
        return $this->belongsTo(User::class, 'scanne_par');
    }
}