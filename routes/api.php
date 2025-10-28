<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
 use App\Http\Controllers\PasswordResetController;
 use App\Http\Controllers\FilmController;

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
Route::get('/movies/popular', [FilmController::class, 'popular']);
Route::get('/movies/search', [FilmController::class, 'search']);
Route::get('/movies/{id}', [FilmController::class, 'details']);
// Route pour films filtrés
Route::get('/movies/filter', [FilmController::class, 'filtered']);

});

