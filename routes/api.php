<?php

use App\Ui\Http\Controller\Api\CommentController;
use App\Ui\Http\Controller\Api\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Ui\Http\Controller\Api\AuthController;

/*
|--------------------------------------------------------------------------
| Routes d'authentification
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| Routes PUBLIQUES
|--------------------------------------------------------------------------
*/
Route::prefix('profiles')->group(function () {
    Route::get('/', [ProfileController::class, 'publicIndex']); // lister profils actifs
});


/*
|--------------------------------------------------------------------------
| Routes PROTÉGÉES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Profiles
    Route::get('/admin/profiles', [ProfileController::class, 'adminIndex']); // lister les profil (avec status)
    Route::post('profiles', [ProfileController::class, 'store']); // Créer un profil
    Route::put('/profiles/{profile}', [ProfileController::class, 'update']); // Modifier un profil
    Route::post('/profiles/{profile}/image', [ProfileController::class, 'updateProfilImage']); // Modifier l'image d'un profil
    Route::patch('/profiles/{profile}', [ProfileController::class, 'update']); // Modifier un profil partiel
    Route::delete('/profiles/{profile}', [ProfileController::class, 'destroy']); // supprimer un profil

    // Comments
    Route::post('/profiles/{profile}/comments', [CommentController::class, 'store']); // Ajouter un commentaire
});
