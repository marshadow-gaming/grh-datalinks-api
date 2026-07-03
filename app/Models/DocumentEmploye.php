<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentEmploye extends Model
{
    protected $table = 'documents_employe';

    protected $fillable = [
        'employe_id', 'type', 'nom_fichier', 'chemin_fichier'
    ];

    public function employe() {
        return $this->belongsTo(Employe::class);
    }
}