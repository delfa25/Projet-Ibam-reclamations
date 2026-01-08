<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReclamationController;
use App\Http\Controllers\Api\MatiereController;
use App\Http\Controllers\Api\JustificatifController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Matières
    Route::get('/matieres', [MatiereController::class, 'index']);
    Route::get('/matieres/{matiere}', [MatiereController::class, 'show']);
    
    // Réclamations
    Route::apiResource('reclamations', ReclamationController::class);
    Route::post('reclamations/{reclamation}/soumettre', [ReclamationController::class, 'soumettre']);
    Route::put('reclamations/{reclamation}/verifier', [ReclamationController::class, 'verifier']);
    Route::put('reclamations/{reclamation}/imputer', [ReclamationController::class, 'imputer']);
    Route::put('reclamations/{reclamation}/traiter', [ReclamationController::class, 'traiter']);
    
    // Justificatifs
    Route::post('reclamations/{reclamation}/justificatifs', [JustificatifController::class, 'store']);
    Route::get('justificatifs/{justificatif}/download', [JustificatifController::class, 'download']);
    Route::delete('justificatifs/{justificatif}', [JustificatifController::class, 'destroy']);
});