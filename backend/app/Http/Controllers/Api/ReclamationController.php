<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReclamationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('DA')) {
            return Reclamation::with(['etudiant', 'matiere', 'enseignant'])->latest()->paginate(20);
        }

        if ($user->hasRole('SCOLARITE')) {
            return Reclamation::with(['etudiant', 'matiere', 'enseignant'])
                ->whereIn('status', ['SOUMIS', 'TRANSMIS_SCOLARITE', 'TRAITE', 'REJETE', 'RECEVABLE'])
                ->latest()
                ->paginate(20);
        }

        if ($user->hasRole('ENSEIGNANT')) {
            return Reclamation::with(['etudiant', 'matiere', 'enseignant'])
                ->where('enseignant_id', $user->id)
                ->where('status', '!=', 'BROUILLON')
                ->latest()
                ->paginate(20);
        }

        // Etudiant
        return Reclamation::with(['etudiant', 'matiere', 'enseignant'])
            ->where('etudiant_id', $user->id)
            ->latest()
            ->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'objet' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'required|string',
                'matiere_id' => 'required|exists:matieres,id',
                'piece_jointe' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);

            \DB::transaction(function() use ($request, &$reclamation) {
                $path = null;
                if ($request->hasFile('piece_jointe')) {
                    $path = $request->file('piece_jointe')->store('justificatifs', 'public');
                }

                $reclamation = Reclamation::create([
                    'objet' => $request->objet,
                    'message' => $request->message,
                    'type' => $request->type,
                    'status' => 'BROUILLON',
                    'etudiant_id' => Auth::id(),
                    'matiere_id' => $request->matiere_id,
                    'piece_jointe' => $path,
                ]);
            });

            return response()->json($reclamation, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
             return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur serveur: ' . $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reclamation $reclamation)
    {
        $this->authorize('view', $reclamation);
        return $reclamation->load(['etudiant', 'matiere', 'enseignant', 'justificatifs']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reclamation $reclamation)
    {
        $this->authorize('update', $reclamation);

        $reclamation->update($request->only(['objet', 'message', 'type', 'matiere_id']));

        return response()->json($reclamation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reclamation $reclamation)
    {
        $this->authorize('delete', $reclamation);
        $reclamation->delete();
        return response()->json(null, 204);
    }

    // --- Actions spécifiques du Workflow ---

    public function soumettre(Reclamation $reclamation)
    {
        $this->authorize('soumettre', $reclamation);
        
        $reclamation->update([
            'status' => 'SOUMIS',
            'date_soumission' => now(),
        ]);

        return response()->json($reclamation);
    }

    // Etape 2: Scolarité vérifie la recevabilité
    public function verifier(Request $request, Reclamation $reclamation)
    {
        $this->authorize('verifier', $reclamation);

        $status = $request->recevable ? 'RECEVABLE' : 'REJETE';
        
        \DB::transaction(function() use ($reclamation, $status, $request) {
            $reclamation->update(['status' => $status]);

            if (!$request->recevable) {
                try {
                    \App\Jobs\SendReclamationEmail::dispatch(
                        $reclamation->etudiant->email,
                        new \App\Mail\ReclamationRejetee($reclamation)
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur envoi mail rejet: ' . $e->getMessage());
                }
            }
        });

        return response()->json($reclamation);
    }

    // Etape 3: DA impute à l'enseignant
    public function imputer(Request $request, Reclamation $reclamation)
    {
        $this->authorize('imputer', $reclamation); // Policy check: User is DA
        
        $reclamation->update([
            'status' => 'EN_TRAITEMENT',
            'enseignant_id' => $request->enseignant_id,
        ]);

        return response()->json($reclamation);
    }

    // Etape 4: Enseignant traite (Valide ou Invalide)
    public function traiter(Request $request, Reclamation $reclamation)
    {
        $this->authorize('traiter', $reclamation); // Policy check: User is ENSEIGNANT

        $decision = $request->valide ? 'VALIDE_ENSEIGNANT' : 'INVALIDE_ENSEIGNANT';

        $reclamation->update([
            'status' => $decision,
            'commentaire_enseignant' => $request->commentaire,
            'date_traitement' => now(),
            'note_corrigee' => $request->valide ? $request->note_corrigee : null, // Si valide, on peut proposer la nouvelle note
        ]);

        return response()->json($reclamation);
    }
    
    // Etape 5: DA transmet la décision à la scolarité
    public function transmettreScolarite(Request $request, Reclamation $reclamation)
    {
         // Cette action est faite par le DA après retour de l'enseignant
         // $this->authorize('transmettre', $reclamation); // To be added to policy
         if (!$request->user()->hasRole('DA')) {
             abort(403, 'Seul le DA peut transmettre à la scolarité.');
         }

         $reclamation->update([
            'status' => 'TRANSMIS_SCOLARITE', 
         ]);
         return response()->json($reclamation);
    }

    // Etape 6: Scolarité finalise (Corrige la note ou clôture)
    public function finaliser(Request $request, Reclamation $reclamation)
    {
        $this->authorize('finaliser', $reclamation);

        \DB::transaction(function() use ($reclamation, $request) {
            $reclamation->update([
                'status' => 'TRAITE',
                'commentaire_scolarite' => $request->commentaire,
                'date_validation' => now(),
            ]);

            try {
                \App\Jobs\SendReclamationEmail::dispatch(
                    $reclamation->etudiant->email,
                    new \App\Mail\ReclamationTraitee($reclamation)
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erreur envoi mail: ' . $e->getMessage());
            }
        });

        return response()->json($reclamation);
    }

    public function downloadPieceJointe(Reclamation $reclamation)
    {
        $this->authorize('view', $reclamation);

        if (!$reclamation->piece_jointe) {
            return response()->json(['message' => 'Aucun fichier joint'], 404);
        }

        // Utiliser le disque 'public' car c'est là qu'on a stocké le fichier via store(..., 'public')
        return Storage::disk('public')->download($reclamation->piece_jointe);
    }
}
