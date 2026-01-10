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
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:ETUDIANT,ENSEIGNANT',
            'filiere_id' => 'required|exists:filieres,id',
            'telephone' => 'nullable|string',
            'niveau' => 'required_if:role,ETUDIANT|string'
        ]);

        // Génération automatique des matricules
        if ($request->role === 'ETUDIANT') {
            $ine = 'INE' . date('Y') . str_pad(User::where('role', 'ETUDIANT')->count() + 1, 6, '0', STR_PAD_LEFT);
            $matricule = $ine; // Pour l'étudiant, INE = matricule
        } else {
            $matricule = 'ENS' . date('Y') . str_pad(User::where('role', 'ENSEIGNANT')->count() + 1, 6, '0', STR_PAD_LEFT);
            $ine = null;
        }

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => $request->role,
            'matricule' => $matricule,
            'filiere_id' => $request->filiere_id,
            'telephone' => $request->telephone,
            'ine' => $ine,
            'niveau' => $request->niveau
        ]);

        // Si c'est un enseignant, attribuer les matières sélectionnées
        if ($request->role === 'ENSEIGNANT' && $request->has('matiere_ids')) {
            \App\Models\Matiere::whereIn('id', $request->matiere_ids)
                ->update(['enseignant_id' => $user->id]);
        }

        return response()->json($user->load('filiere'), 201);
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