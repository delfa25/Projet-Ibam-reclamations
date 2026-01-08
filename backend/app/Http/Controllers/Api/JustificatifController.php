<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Justificatif;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JustificatifController extends Controller
{
    public function store(Request $request, Reclamation $reclamation)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $file = $request->file('fichier');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('justificatifs', $filename, 'public');

        $justificatif = Justificatif::create([
            'reclamation_id' => $reclamation->id,
            'nom_fichier' => $file->getClientOriginalName(),
            'chemin_fichier' => $path,
            'type_fichier' => $file->getClientOriginalExtension(),
            'taille' => $file->getSize()
        ]);

        return response()->json($justificatif, 201);
    }

    public function download(Justificatif $justificatif)
    {
        if (!Storage::disk('public')->exists($justificatif->chemin_fichier)) {
            return response()->json(['message' => 'Fichier introuvable'], 404);
        }

        return Storage::disk('public')->download(
            $justificatif->chemin_fichier,
            $justificatif->nom_fichier
        );
    }

    public function destroy(Justificatif $justificatif)
    {
        Storage::disk('public')->delete($justificatif->chemin_fichier);
        $justificatif->delete();
        
        return response()->json(['message' => 'Justificatif supprimé']);
    }
}
