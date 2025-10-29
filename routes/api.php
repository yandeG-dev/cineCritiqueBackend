<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
 use App\Http\Controllers\PasswordResetController;
 use App\Http\Controllers\FilmController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\UserController;
 //Route for authentication and password reset
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
      Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])->name('forgot');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('reset');
});
//routes for movie data


Route::middleware('api')->group(function () {
 Route::get('/movies/popular', [FilmController::class, 'getPopular'])->name('movies.popular');
 Route::get('/movies', [FilmController::class, 'index']);

    Route::get('/movies/{id}', [FilmController::class, 'details'])->name('movies.details');
 

});
//routes for reviews (avis)

Route::middleware('auth:api')->group(function () {
    Route::post('/avis', [AvisController::class, 'store']); // Ajouter un avis
    //Route::get('/films/{film_id}/avis', [AvisController::class, 'index']); // Voir avis d’un film
    Route::put('/avis/{id}', [AvisController::class, 'update']); // Modifier un avis
    Route::delete('/avis/{id}', [AvisController::class, 'destroy']); // Supprimer un avis
  

});
// Routes publiques pour afficher les avis d’un film
Route::get('/films/{filmId}/avis', [AvisController::class, 'indexByFilm']);
  //Route::get('/avis/film/{film_id}', [AvisController::class, 'showByFilm']);

//routes profile utilisateur
  Route::middleware('auth:api')->group(function () {
    Route::get('/user/{id}/profile', [UserController::class, 'profile']);
    Route::put('/user/{id}/bio', [UserController::class, 'updateBio']);
});
