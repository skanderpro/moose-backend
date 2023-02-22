<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guess;
use App\Models\Season;
use App\Models\Team;
use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ScoresController extends Controller
{
    public function index()
    {
        $season = Season::getActive();
        /** @var Collection $games */
        $games = $season->games->groupBy('type');
        $guess = Guess::getForUser($season, Auth::user());

        $teamMapper = fn($game) => [Team::find($game->first_team_id), Team::find($game->second_team_id)];

        return view('scores', [
            'seasons' => $season,
            'games_left' => $games['left']->map($teamMapper)->toArray(),
            'games_right' => $games['right']->map($teamMapper)->toArray(),
            'left' => $guess ? $guess->getResults('left') : [],
            'right' => $guess ? $guess->getResults('right') : [],
            'final' => $guess ? $guess->getResults('final') : [],
        ]);
    }

    public function store(Request $request, Season $season)
    {
        if (NOW()->isAfter($season->start)) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $payload = Validator::make($request->request->all(), [
                'type' => 'required|in:left,right,final',
                'results' => 'required|array',
            ])->validate();

            $user = Auth::user();
            $guess = Guess::getForUser($season, $user);
            if (!$guess) {
                $guess = Guess::create([
                    'season_id' => $season->id,
                    'user_id' => $user->id,
                    'results_left' => '[]',
                    'results_right' => '[]',
                    'results_final' => '[]',
                ]);
            }

            $guess->{"results_" . trim($payload['type'])} = json_encode($payload['results']);
            $guess->save();

            return response()->json([], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
