<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class IbamSeeder extends Seeder
{
    public function run(): void
    {
        // Filières
        $filieres = [
            ['code_filiere' => 'INFO', 'nom_filiere' => 'Informatique'],
            ['code_filiere' => 'ELEC', 'nom_filiere' => 'Électronique'],
            ['code_filiere' => 'MECA', 'nom_filiere' => 'Mécanique'],
        ];
        
        foreach ($filieres as $filiere) {
            DB::table('filieres')->insert(array_merge($filiere, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Utilisateurs de test
        $users = [
            [
                'nom' => 'Test',
                'prenom' => 'Étudiant',
                'email' => 'etudiant@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'ETUDIANT',
                'matricule' => 'ETU2024001',
                'filiere_id' => 1,
                'telephone' => '70123456',
                'ine' => 'INE2024000001',
                'niveau' => 'L1'
            ],
            [
                'nom' => 'Scolarité',
                'prenom' => 'Agent',
                'email' => 'scolarite@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'SCOLARITE',
                'matricule' => 'SCO2024001',
                'telephone' => '70654321'
            ],
            [
                'nom' => 'Info',
                'prenom' => 'Enseignant',
                'email' => 'enseignant@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'ENSEIGNANT',
                'matricule' => 'ENS2024001',
                'filiere_id' => 1,
                'telephone' => '70987654'
            ],
            [
                'nom' => 'Adjoint',
                'prenom' => 'Directeur',
                'email' => 'da@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'DA',
                'matricule' => 'DA2024001',
                'telephone' => '70456789'
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Matières
        $matieres = [
            ['code_matiere' => 'PROG1', 'nom_matiere' => 'Programmation 1', 'credit' => 4, 'filiere_id' => 1, 'enseignant_id' => 3],
            ['code_matiere' => 'BDD1', 'nom_matiere' => 'Base de Données 1', 'credit' => 3, 'filiere_id' => 1, 'enseignant_id' => 3],
            ['code_matiere' => 'MATH1', 'nom_matiere' => 'Mathématiques 1', 'credit' => 5, 'filiere_id' => 1, 'enseignant_id' => 3],
        ];

        foreach ($matieres as $matiere) {
            DB::table('matieres')->insert(array_merge($matiere, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
