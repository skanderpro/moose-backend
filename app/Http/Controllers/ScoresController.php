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
        $user = Auth::user();
        $season = Season::getActive();
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

        return redirect()->route('scores.variant', [
            'guess' => $guess,
        ]);
    }

    public function variant(Guess $guess)
    {
        $season = Season::getActive();
        /** @var Collection $games */
        $games = $season->games->groupBy('type');
        $guesses = Guess::getListForUser($season, Auth::user());

        $teamMapper = fn($game) => [Team::findForSeason($game->first_team_id, $season), Team::findForSeason($game->second_team_id, $season)];

        return view('scores', [
            'season' => $season,
            'guesses' => $guesses,
            'currentGuess' => $guess,
            'games_left' => $games['left']->map($teamMapper)->toArray(),
            'games_right' => $games['right']->map($teamMapper)->toArray(),
            'left' => $guess->getResults('left'),
            'right' => $guess->getResults('right'),
            'final' => $guess->getResults('final'),
        ]);
    }

    public function store(Request $request, Season $season, Guess $guess)
    {
        if (NOW()->isAfter($season->start)) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $payload = Validator::make($request->request->all(), [
                'type' => 'required|in:left,right,final',
                'results' => 'required|array',
            ])->validate();

            $guess->{"results_" . trim($payload['type'])} = json_encode($payload['results']);
            $guess->save();

            return response()->json([], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function createGuess()
    {
        $user = Auth::user();
        $season = Season::getActive();
        $guess = Guess::create([
            'season_id' => $season->id,
            'user_id' => $user->id,
            'results_left' => '[]',
            'results_right' => '[]',
            'results_final' => '[]',
        ]);

        return redirect()->route('scores.variant', [
            'guess' => $guess,
        ]);
    }
}
