<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReclamationRequest;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReclamationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Reclamation::with(['etudiant', 'matiere', 'enseignant', 'justificatifs']);

        // Filtrage par rôle
        switch ($user->role) {
            case 'ETUDIANT':
                $query->where('etudiant_id', $user->id);
                break;
            case 'ENSEIGNANT':
                $query->whereHas('matiere', fn($q) => $q->where('filiere_id', $user->filiere_id))
                      ->whereIn('statut', ['IMPUTEE_ENSEIGNANT', 'VALIDEE', 'INVALIDEE']);
                break;
            case 'SCOLARITE':
                $query->whereNotIn('statut', ['BROUILLON']);
                break;
            case 'DA':
                // Voir toutes les réclamations
                break;
        }

        return response()->json($query->latest()->get());
    }

    public function store(StoreReclamationRequest $request)
    {
        try {
            // Récupérer la matière pour obtenir la filière
            $matiere = \App\Models\Matiere::with('filiere')->find($request->matiere_id);
            
            if (!$matiere) {
                return response()->json(['message' => 'Matière non trouvée'], 422);
            }

            $reclamation = Reclamation::create([
                'numero_demande' => 'REC-' . date('Y') . '-' . str_pad(Reclamation::count() + 1, 4, '0', STR_PAD_LEFT),
                'etudiant_id' => $request->user()->id,
                'matiere_id' => $request->matiere_id,
                'enseignant_id' => $matiere->enseignant_id ?? null,
                'objet_demande' => $request->objet_demande,
                'motif' => $request->motif,
                'statut' => 'BROUILLON'
            ]);

            // Gérer l'upload du justificatif
            if ($request->hasFile('justificatif')) {
                $file = $request->file('justificatif');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('justificatifs', $fileName, 'public');
                
                \App\Models\Justificatif::create([
                    'reclamation_id' => $reclamation->id,
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin_fichier' => $filePath,
                    'type_fichier' => $file->getMimeType(),
                    'taille' => $file->getSize()
                ]);
            }

            return response()->json($reclamation->load(['matiere', 'etudiant', 'justificatifs']), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur validation réclamation: ' . json_encode($e->errors()));
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur création réclamation: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la création de la réclamation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Reclamation $reclamation)
    {
        return response()->json($reclamation->load(['etudiant', 'matiere', 'enseignant', 'justificatifs', 'historique']));
    }

    public function update(Request $request, Reclamation $reclamation)
    {
        if ($reclamation->statut !== 'BROUILLON') {
            return response()->json(['message' => 'Impossible de modifier une réclamation soumise'], 403);
        }

        $request->validate([
            'filiere_id' => 'sometimes|exists:filieres,id',
            'matiere_id' => 'sometimes|exists:matieres,id',
            'objet_demande' => 'sometimes|string|max:255',
            'motif' => 'sometimes|string|min:10',
            'justificatif' => 'sometimes|file|mimes:jpeg,jpg,png,pdf|max:5120'
        ]);

        // Vérifier la cohérence filière/matière si les deux sont fournis
        if ($request->has(['filiere_id', 'matiere_id'])) {
            $matiere = \App\Models\Matiere::where('id', $request->matiere_id)
                                          ->where('filiere_id', $request->filiere_id)
                                          ->first();
            
            if (!$matiere) {
                return response()->json(['message' => 'La matière ne correspond pas à la filière sélectionnée'], 422);
            }
        }

        $reclamation->update($request->only(['matiere_id', 'objet_demande', 'motif']));

        // Gérer le nouveau justificatif si fourni
        if ($request->hasFile('justificatif')) {
            // Supprimer l'ancien justificatif
            $ancienJustificatif = $reclamation->justificatifs()->first();
            if ($ancienJustificatif) {
                \Storage::disk('public')->delete($ancienJustificatif->chemin_fichier);
                $ancienJustificatif->delete();
            }

            // Ajouter le nouveau
            $file = $request->file('justificatif');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('justificatifs', $fileName, 'public');
            
            \App\Models\Justificatif::create([
                'reclamation_id' => $reclamation->id,
                'nom_fichier' => $file->getClientOriginalName(),
                'chemin_fichier' => $filePath,
                'type_fichier' => $file->getMimeType(),
                'taille' => $file->getSize()
            ]);
        }

        return response()->json($reclamation->load(['matiere', 'etudiant', 'justificatifs']));
    }

    public function destroy(Reclamation $reclamation)
    {
        if ($reclamation->statut !== 'BROUILLON') {
            return response()->json(['message' => 'Impossible de supprimer une réclamation soumise'], 403);
        }

        $reclamation->delete();
        return response()->json(['message' => 'Réclamation supprimée']);
    }

    public function soumettre(Reclamation $reclamation)
    {
        if ($reclamation->statut !== 'BROUILLON') {
            return response()->json(['message' => 'Réclamation déjà soumise'], 400);
        }

        // Vérifier que tous les champs obligatoires sont remplis
        if (!$reclamation->matiere_id || !$reclamation->objet_demande || !$reclamation->motif) {
            return response()->json(['message' => 'Tous les champs obligatoires doivent être remplis'], 422);
        }

        // Vérifier qu'un justificatif est attaché (optionnel pour le brouillon)
        // if (!$reclamation->justificatifs()->exists()) {
        //     return response()->json(['message' => 'Un justificatif est obligatoire pour soumettre la réclamation'], 422);
        // }

        $reclamation->update([
            'statut' => 'SOUMISE',
            'date_soumission' => now()
        ]);

        return response()->json(['message' => 'Réclamation soumise avec succès']);
    }

    public function verifier(Request $request, Reclamation $reclamation)
    {
        $request->validate([
            'decision' => 'required|in:RECEVABLE,REJETEE',
            'commentaire' => 'nullable|string'
        ]);

        $reclamation->update([
            'statut' => $request->decision,
            'commentaire_scolarite' => $request->commentaire
        ]);

        return response()->json(['message' => 'Réclamation vérifiée']);
    }

    public function imputer(Request $request, Reclamation $reclamation)
    {
        $reclamation->update([
            'statut' => 'IMPUTEE_ENSEIGNANT',
            'enseignant_id' => $reclamation->matiere->enseignant_id
        ]);

        return response()->json(['message' => 'Réclamation imputée à l\'enseignant']);
    }

    public function traiter(Request $request, Reclamation $reclamation)
    {
        $request->validate([
            'decision' => 'required|in:VALIDEE,INVALIDEE',
            'note_corrigee' => 'required_if:decision,VALIDEE|numeric|min:0|max:20',
            'commentaire' => 'nullable|string'
        ]);

        $reclamation->update([
            'statut' => $request->decision,
            'note_corrigee' => $request->note_corrigee,
            'decision_enseignant' => $request->commentaire,
            'date_traitement' => now()
        ]);

        return response()->json(['message' => 'Réclamation traitée']);
    }
}
