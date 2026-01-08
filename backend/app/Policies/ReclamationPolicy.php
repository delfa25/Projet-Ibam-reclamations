<?php

namespace App\Policies;

use App\Models\Reclamation;
use App\Models\User;

class ReclamationPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['ETUDIANT', 'SCOLARITE', 'ENSEIGNANT', 'DA']);
    }

    public function view(User $user, Reclamation $reclamation)
    {
        return match($user->role) {
            'ETUDIANT' => $reclamation->etudiant_id === $user->id,
            'ENSEIGNANT' => $reclamation->matiere->filiere_id === $user->filiere_id,
            'SCOLARITE', 'DA' => true,
            default => false
        };
    }

    public function create(User $user)
    {
        return $user->role === 'ETUDIANT';
    }

    public function update(User $user, Reclamation $reclamation)
    {
        return $user->role === 'ETUDIANT' 
            && $reclamation->etudiant_id === $user->id 
            && $reclamation->statut === 'BROUILLON';
    }

    public function delete(User $user, Reclamation $reclamation)
    {
        return $this->update($user, $reclamation);
    }

    public function verifier(User $user)
    {
        return $user->role === 'SCOLARITE';
    }

    public function imputer(User $user)
    {
        return $user->role === 'DA';
    }

    public function traiter(User $user, Reclamation $reclamation)
    {
        return $user->role === 'ENSEIGNANT' 
            && $reclamation->matiere->filiere_id === $user->filiere_id;
    }
}
