<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// ─── Fórum público (navbar + rodapé) ─────────────────────────
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/categoria/{slug}', [SiteController::class, 'category'])->name('category');
Route::get('/topico/{slug}', [SiteController::class, 'topic'])->name('topic');
Route::get('/membro/{slug}', [SiteController::class, 'member'])->name('member');
Route::get('/busca', [SiteController::class, 'search'])->name('search');
Route::get('/regras', [SiteController::class, 'rules'])->name('rules');

// ─── Autenticação (só visitante, layout em branco) ───────────
Route::middleware('guest.mock')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Moderação (sidebar + topbar, auth-gated mock) ───────────
Route::middleware('auth.mock')->group(function () {
    Route::get('/app', [AppController::class, 'dashboard'])->name('dashboard');
    Route::get('/app/topicos', [AppController::class, 'topics'])->name('app.topics');
    Route::get('/app/topicos/{id}', [AppController::class, 'topicDetail'])->name('app.topic-detail');
    Route::get('/app/denuncias', [AppController::class, 'reports'])->name('app.reports');
    Route::get('/app/categorias', [AppController::class, 'categories'])->name('app.categories');
    Route::get('/app/membros', [AppController::class, 'members'])->name('app.members');
    Route::get('/app/configuracoes', [AppController::class, 'settings'])->name('app.settings');
});

// ─── Fallback 404 ────────────────────────────────────────────
Route::fallback(fn () => response()->view('pages.not-found', ['pageTitle' => '404'], 404));
