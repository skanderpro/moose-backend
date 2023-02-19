<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Team;
use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class ScoresController extends Controller
{
    public function index()
    {
        $season = Season::getActive();
        /** @var Collection $games */
        $games = $season->games->groupBy('type');

        $teamMapper = fn($game) => [Team::find($game->first_team_id), Team::find($game->second_team_id)];

        return view('scores', [
            'season' => $season,
            'games_left' => $games['left']->map($teamMapper)->toArray(),
            'games_right' => $games['right']->map($teamMapper)->toArray(),
        ]);
    }
}
