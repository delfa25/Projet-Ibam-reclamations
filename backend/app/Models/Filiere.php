<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    protected $fillable = [
        'code_filiere',
        'nom_filiere'
    ];

    public function matieres(): HasMany
    {
        return $this->hasMany(Matiere::class);
    }

    public function enseignants(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'ENSEIGNANT');
    }

    public function etudiants(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'ETUDIANT');
    }
}
