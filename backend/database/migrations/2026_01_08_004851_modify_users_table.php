<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['ETUDIANT', 'SCOLARITE', 'ENSEIGNANT', 'DA'])->after('email');
            $table->string('matricule')->nullable()->unique()->after('role');
            $table->foreignId('filiere_id')->nullable()->constrained('filieres')->onDelete('set null')->after('matricule');
            $table->string('telephone')->nullable()->after('filiere_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'matricule', 'filiere_id', 'telephone']);
        });
    }
};
