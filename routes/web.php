<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProcurementController;
use Illuminate\Support\Facades\Route;

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Root redirect
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

// Protected Application Routes
Route::middleware('auth')->group(function () {
    // Home Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Procurements
    Route::resource('procurements', ProcurementController::class);

    // Criteria (nested under procurement, JSON responses)
    Route::prefix('procurements/{procurement}/criteria')->name('procurements.criteria.')->group(function () {
        Route::post('/',              [CriteriaController::class, 'store'])->name('store');
        Route::put('/{criterion}',   [CriteriaController::class, 'update'])->name('update');
        Route::delete('/{criterion}',[CriteriaController::class, 'destroy'])->name('destroy');
    });

    // Candidates (nested under procurement)
    Route::prefix('procurements/{procurement}/candidates')->name('procurements.candidates.')->group(function () {
        Route::get('/create',              [CandidateController::class, 'create'])->name('create');
        Route::post('/',                   [CandidateController::class, 'store'])->name('store');
        Route::get('/{candidate}',         [CandidateController::class, 'show'])->name('show');
        Route::delete('/{candidate}',      [CandidateController::class, 'destroy'])->name('destroy');
        Route::get('/{candidate}/status',  [CandidateController::class, 'status'])->name('status');
        Route::post('/{candidate}/reanalyze', [CandidateController::class, 'reanalyze'])->name('reanalyze');
    });
});
