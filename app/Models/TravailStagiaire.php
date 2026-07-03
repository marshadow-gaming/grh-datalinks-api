<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravailStagiaire extends Model
{
    protected $table = 'travaux_stagiaire';

    protected $fillable = ['employe_id', 'date', 'titre', 'description'];

    public function employe() {
        return $this->belongsTo(Employe::class);
    }
}