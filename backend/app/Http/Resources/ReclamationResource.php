<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReclamationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_demande' => $this->numero_demande,
            'objet_demande' => $this->objet_demande,
            'motif' => $this->motif,
            'statut' => $this->statut,
            'note_actuelle' => $this->note_actuelle,
            'note_corrigee' => $this->note_corrigee,
            'commentaire_scolarite' => $this->commentaire_scolarite,
            'decision_enseignant' => $this->decision_enseignant,
            'date_creation' => $this->date_creation?->format('d/m/Y H:i'),
            'date_soumission' => $this->date_soumission?->format('d/m/Y H:i'),
            'date_traitement' => $this->date_traitement?->format('d/m/Y H:i'),
            'etudiant' => [
                'id' => $this->etudiant->id,
                'name' => $this->etudiant->name,
                'matricule' => $this->etudiant->matricule
            ],
            'matiere' => [
                'id' => $this->matiere->id,
                'nom_matiere' => $this->matiere->nom_matiere,
                'code_matiere' => $this->matiere->code_matiere
            ],
            'enseignant' => $this->when($this->enseignant, [
                'id' => $this->enseignant?->id,
                'name' => $this->enseignant?->name
            ]),
            'justificatifs' => $this->whenLoaded('justificatifs'),
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i')
        ];
    }
}
