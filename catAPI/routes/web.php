<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

// Página de registro (exibe o formulário)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

// Processar o registro (recebe os dados do formulário)
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Página de login (exibe o formulário)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Processar o login (recebe os dados do formulário)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rota principal (home) direcionando para a página de gatos
Route::get('/', function () {
    return redirect()->route('cats');
});

// Rota para a página de gatos (acessível a todos)
Route::get('/cats', function () {
    return view('cats'); // Certifique-se de que a view 'cats' exista
})->name('cats');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::post('/favorite', [CatController::class, 'favorite'])->name('favorite');
    Route::delete('/favorite/{catId}', [CatController::class, 'deleteFavorite'])->name('favorite.delete');
    Route::get('/favorites', [CatController::class, 'showFavorites'])->name('favorites');
    Route::get('/api/favorites', [CatController::class, 'getFavorites'])->name('api.favorites');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});