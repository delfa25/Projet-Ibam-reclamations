<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'valeur',
        'type_evaluation',
        'date_evaluation',
        'annee_academique'
    ];

    protected $casts = [
        'valeur' => 'decimal:2',
        'date_evaluation' => 'date'
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }
}
