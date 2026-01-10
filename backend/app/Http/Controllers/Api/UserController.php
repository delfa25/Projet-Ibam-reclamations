<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'role' => 'required|in:ETUDIANT,ENSEIGNANT',
                'filiere_id' => 'required|exists:filieres,id',
                'telephone' => 'nullable|string',
                'niveau' => 'nullable|string',
                'matiere_ids' => 'nullable|array',
                'matiere_ids.*' => 'exists:matieres,id'
            ]);

            // Génération automatique des matricules
            if ($validated['role'] === 'ETUDIANT') {
                $ine = 'INE' . date('Y') . str_pad(User::where('role', 'ETUDIANT')->count() + 1, 6, '0', STR_PAD_LEFT);
                $matricule = $ine;
            } else {
                $matricule = 'ENS' . date('Y') . str_pad(User::where('role', 'ENSEIGNANT')->count() + 1, 6, '0', STR_PAD_LEFT);
                $ine = null;
            }

            $user = User::create([
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'password' => Hash::make('password123'),
                'role' => $validated['role'],
                'matricule' => $matricule,
                'filiere_id' => $validated['filiere_id'],
                'telephone' => $validated['telephone'] ?? null,
                'ine' => $ine,
                'niveau' => $validated['niveau'] ?? null
            ]);

            // Si c'est un enseignant, attribuer les matières sélectionnées
            if ($validated['role'] === 'ENSEIGNANT' && isset($validated['matiere_ids'])) {
                Matiere::whereIn('id', $validated['matiere_ids'])
                    ->update(['enseignant_id' => $user->id]);
            }

            return response()->json($user->load('filiere'), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur création utilisateur: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $role = $request->get('role');
        $query = User::with('filiere');
        
        if ($role) {
            $query->where('role', $role);
        }

        return response()->json($query->get());
    }

    public function show(User $user)
    {
        return response()->json($user->load('filiere'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'matricule' => 'sometimes|string|unique:users,matricule,' . $user->id,
            'filiere_id' => 'sometimes|exists:filieres,id',
            'telephone' => 'nullable|string',
            'ine' => 'sometimes|string|unique:users,ine,' . $user->id,
            'niveau' => 'sometimes|string'
        ]);

        $user->update($request->only([
            'nom', 'prenom', 'email', 'matricule', 'filiere_id', 
            'telephone', 'ine', 'niveau'
        ]));

        return response()->json($user->load('filiere'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}