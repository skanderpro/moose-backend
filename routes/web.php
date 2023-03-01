<?php

use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ScoresController;
use App\Http\Controllers\Voyager\SeasonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'admin'], function () {
    Route::get('/seasons/{season}/results', [SeasonController::class, 'results'])->name('voyager.seasons.results');
    Route::post('/seasons/{season}/results', [SeasonController::class, 'storeResults'])->name('voyager.seasons.store-results');
    Route::post('/seasons/{season}/recalculate', [SeasonController::class, 'runRecalculateJob'])->name('voyager.seasons.recalculate');
    Route::get('/seasons/{season}/reset', [SeasonController::class, 'resetSeason'])->name('voyager.seasons.reset');
    Voyager::routes();
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::post('/score/{season}/{guess}/store', [ScoresController::class, 'store'])->name('scores.store');
    Route::get('/{guess}/variant', [ScoresController::class, 'variant'])->name('scores.variant');
    Route::post('/variant/create', [ScoresController::class, 'createGuess'])->name('scores.variant.create');
    Route::get('/', [ScoresController::class, 'index'])->name('scores');
});

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
