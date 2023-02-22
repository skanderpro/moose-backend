<?php

use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ScoresController;
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
    Route::get('/seasons/{season}/results', [\App\Http\Controllers\Voyager\SeasonController::class, 'results'])->name('voyager.seasons.results');
    Voyager::routes();
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::post('/score/{seasons}/store', [ScoresController::class, 'store'])->name('scores.store');
    Route::get('/', [ScoresController::class, 'index'])->name('scores');
});

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
