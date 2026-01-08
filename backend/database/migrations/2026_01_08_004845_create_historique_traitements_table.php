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
        Schema::create('historique_traitements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reclamation_id')->constrained('reclamations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action');
            $table->string('ancien_statut')->nullable();
            $table->string('nouveau_statut');
            $table->text('commentaire')->nullable();
            $table->timestamp('date_action')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_traitements');
    }
};
