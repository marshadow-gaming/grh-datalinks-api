<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['user_id', 'module', 'accorde_par'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function accordePar() {
        return $this->belongsTo(User::class, 'accorde_par');
    }
}