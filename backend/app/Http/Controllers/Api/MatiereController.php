<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Matiere::with(['filiere', 'enseignant']);

        // Filtrer par filière pour les étudiants
        if ($user->role === 'ETUDIANT' && $user->filiere_id) {
            $query->where('filiere_id', $user->filiere_id);
        }

        return response()->json($query->get());
    }

    public function show(Matiere $matiere)
    {
        return response()->json($matiere->load(['filiere', 'enseignant']));
    }
}
