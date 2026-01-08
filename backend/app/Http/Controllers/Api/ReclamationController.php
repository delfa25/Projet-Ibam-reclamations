<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReclamationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Reclamation::with(['etudiant', 'matiere', 'enseignant']);

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

    public function store(Request $request)
    {
        $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'objet_demande' => 'required|string',
            'motif' => 'required|string'
        ]);

        $reclamation = Reclamation::create([
            'numero_demande' => 'REC-' . date('Y') . '-' . str_pad(Reclamation::count() + 1, 4, '0', STR_PAD_LEFT),
            'etudiant_id' => $request->user()->id,
            'matiere_id' => $request->matiere_id,
            'objet_demande' => $request->objet_demande,
            'motif' => $request->motif,
            'statut' => 'BROUILLON'
        ]);

        return response()->json($reclamation->load(['matiere', 'etudiant']), 201);
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
            'matiere_id' => 'sometimes|exists:matieres,id',
            'objet_demande' => 'sometimes|string',
            'motif' => 'sometimes|string'
        ]);

        $reclamation->update($request->only(['matiere_id', 'objet_demande', 'motif']));

        return response()->json($reclamation->load(['matiere', 'etudiant']));
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
