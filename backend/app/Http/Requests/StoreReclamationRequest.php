<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReclamationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filiere_id' => 'required|exists:filieres,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'required|exists:users,id',
            'objet_demande' => 'required|string|max:255',
            'motif' => 'required|string|min:10',
            'justificatif' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120' // 5MB max
        ];
    }

    public function messages(): array
    {
        return [
            'filiere_id.required' => 'La filière est obligatoire.',
            'filiere_id.exists' => 'La filière sélectionnée n\'existe pas.',
            'matiere_id.required' => 'La matière est obligatoire.',
            'matiere_id.exists' => 'La matière sélectionnée n\'existe pas.',
            'enseignant_id.required' => 'L\'enseignant est obligatoire.',
            'enseignant_id.exists' => 'L\'enseignant sélectionné n\'existe pas.',
            'objet_demande.required' => 'L\'objet de la demande est obligatoire.',
            'objet_demande.max' => 'L\'objet de la demande ne peut pas dépasser 255 caractères.',
            'motif.required' => 'Le motif détaillé est obligatoire.',
            'motif.min' => 'Le motif doit contenir au moins 10 caractères.',
            'justificatif.required' => 'Le justificatif (feuille de copie) est obligatoire.',
            'justificatif.mimes' => 'Le justificatif doit être une image (JPG, PNG) ou un PDF.',
            'justificatif.max' => 'Le justificatif ne peut pas dépasser 5MB.'
        ];
    }
}