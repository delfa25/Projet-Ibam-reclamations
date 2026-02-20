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
        Schema::table('reclamations', function (Blueprint $table) {
            $table->index('etudiant_id');
            $table->index('enseignant_id');
            $table->index('matiere_id');
            $table->index('status');
            $table->index(['status', 'etudiant_id'], 'idx_status_etudiant');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reclamations', function (Blueprint $table) {
            $table->dropIndex(['etudiant_id']);
            $table->dropIndex(['enseignant_id']);
            $table->dropIndex(['matiere_id']);
            $table->dropIndex(['status']);
            $table->dropIndex('idx_status_etudiant');
            $table->dropIndex(['created_at']);
        });
    }
};
