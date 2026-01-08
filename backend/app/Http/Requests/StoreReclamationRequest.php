<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReclamationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'ETUDIANT';
    }

    public function rules(): array
    {
        return [
            'matiere_id' => 'required|exists:matieres,id',
            'objet_demande' => 'required|string|max:255',
            'motif' => 'required|string|min:10'
        ];
    }

    public function messages(): array
    {
        return [
            'matiere_id.required' => 'La matière est obligatoire',
            'matiere_id.exists' => 'Cette matière n\'existe pas',
            'objet_demande.required' => 'L\'objet de la demande est obligatoire',
            'motif.required' => 'Le motif est obligatoire',
            'motif.min' => 'Le motif doit contenir au moins 10 caractères'
        ];
    }
}
