<?php

use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\ProcurementController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('procurements.index'));

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
