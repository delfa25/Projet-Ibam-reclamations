<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justificatif extends Model
{
    protected $fillable = [
        'reclamation_id',
        'nom_fichier',
        'chemin_fichier',
        'type_fichier',
        'taille'
    ];

    protected $casts = [
        'date_ajout' => 'datetime'
    ];

    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(Reclamation::class);
    }
}
