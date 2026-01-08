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
            ['code_filiere' => 'INFO', 'nom_filiere' => 'Informatique', 'departement' => 'Sciences et Technologies'],
            ['code_filiere' => 'ELEC', 'nom_filiere' => 'Électronique', 'departement' => 'Sciences et Technologies'],
            ['code_filiere' => 'MECA', 'nom_filiere' => 'Mécanique', 'departement' => 'Ingénierie'],
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
                'name' => 'Étudiant Test',
                'email' => 'etudiant@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'ETUDIANT',
                'matricule' => 'ETU2024001',
                'filiere_id' => 1,
                'telephone' => '70123456'
            ],
            [
                'name' => 'Agent Scolarité',
                'email' => 'scolarite@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'SCOLARITE',
                'telephone' => '70654321'
            ],
            [
                'name' => 'Enseignant Info',
                'email' => 'enseignant@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'ENSEIGNANT',
                'filiere_id' => 1,
                'telephone' => '70987654'
            ],
            [
                'name' => 'Directeur Adjoint',
                'email' => 'da@ibam.bf',
                'password' => Hash::make('password'),
                'role' => 'DA',
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
