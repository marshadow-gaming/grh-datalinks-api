<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'pin',
        'role', 'telephone', 'photo', 'statut',
    ];

    protected $hidden = ['password', 'remember_token', 'pin'];

    public function employe() {
        return $this->hasOne(Employe::class);
    }

    public function offresPubliees() {
        return $this->hasMany(OffreEmploi::class, 'publiee_par');
    }

    public function entretiensMenes() {
        return $this->hasMany(Entretien::class, 'interviewer_id');
    }

    public function demandesTraitees() {
        return $this->hasMany(DemandeAbsence::class, 'traite_par');
    }
    public function permissions() {
        return $this->hasMany(Permission::class);
    }

    public function hasPermission($module) {
        return $this->permissions()->where('module', $module)->exists();
    }

    public function isAdmin() { return $this->role === 'admin'; }
    public function isDrh() { return $this->role === 'drh'; }
    public function isDirecteur() { return $this->role === 'directeur'; }
    public function isEmploye() { return $this->role === 'employe'; }
    public function isStagiaire() { return $this->role === 'stagiaire'; }
}