<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Guess extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'user_id',
        'results_left',
        'results_right',
        'results_final',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getResults(string $type)
    {
        $value = $this->{'results_' . $type};
        if (empty($value)) {
            return [];
        }

        $json = json_decode($value, true);
        if (!is_array($json)) {
            $json = [];
        }

        return $json;
    }

    public static function getForUser(Season $season, User $user)
    {
        return static::where('season_id', $season->id)->where('user_id', $user->id)->first();
    }

    protected function calculateGuessScore($rating, $level)
    {
        $bonus = 0;

        switch ($level) {
            case 2;
                $bonus = 10;
                break;
            case 3:
                $bonus = 25;
                break;
            case 4:
                $bonus = 40;
                break;
            case 5:
                $bonus = 65;
                break;
        }

        return $rating * ($level + 1) + $bonus;
    }

    public function calculateScore(Season $season)
    {
        $score = 0;
        $left = $season->getResults('left');
        $right = $season->getResults('right');
        $final = $season->getResults('final');
        $guessLeft = $this->getResults('left');
        $guessRight = $this->getResults('right');
        $guessFinal = $this->getResults('final');
        $games = $season->games->groupBy('type');
        $teamMapper = fn($game) => [Team::findForSeason($game->first_team_id, $season), Team::findForSeason($game->second_team_id, $season)];
        $games_left = $games['left']->map($teamMapper)->toArray();
        $games_right = $games['right']->map($teamMapper)->toArray();

        // left games
        foreach ($left as $level => $levelGames) {
            $gamesRegistry = [];

            foreach ($levelGames as $gameIndex => $games) {
                foreach ($games as $resultIndex => $result) {
                    $guessResult = $guessLeft[$level][$gameIndex][$resultIndex] ?? [null, null];
                    $teams = $games_left[$gameIndex];
                    $winner = $result[0] > $result[1] ? $teams[0] : $teams[1];
                    $rating = $winner->rating;
                    $lastItemIndex = count($gamesRegistry) - 1;

                    if ($lastItemIndex > -1 && count($gamesRegistry[$lastItemIndex]) == 1) {
                        $gamesRegistry[$lastItemIndex][] = $winner;
                    } else {
                        $gamesRegistry[] = [$winner];
                    }

                    if (
                        $guessResult[0] === null ||
                        $guessResult[1] === null ||
                        $result[0] === null ||
                        $result[1] === null
                    ) {
                        continue;
                    } elseif (
                        $guessResult[0] == $result[0] &&
                        $guessResult[1] == $result[1]
                    ) {
                        $score += $this->calculateGuessScore($rating, $level);
                    }
                }
            }
        }

        // right games
        foreach ($right as $level => $levelGames) {
            $gamesRegistry = [];

            foreach ($levelGames as $gameIndex => $games) {
                foreach ($games as $resultIndex => $result) {
                    $guessResult = $guessRight[$level][$gameIndex][$resultIndex] ?? [null, null];
                    $teams = $games_right[$gameIndex];
                    $winner = $result[0] > $result[1] ? $teams[0] : $teams[1];
                    $rating = $winner->rating;
                    $lastItemIndex = count($gamesRegistry) - 1;

                    if ($lastItemIndex > -1 && count($gamesRegistry[$lastItemIndex]) == 1) {
                        $gamesRegistry[$lastItemIndex][] = $winner;
                    } else {
                        $gamesRegistry[] = [$winner];
                    }

                    if (
                        $guessResult[0] === null ||
                        $guessResult[1] === null ||
                        $result[0] === null ||
                        $result[1] === null
                    ) {
                        continue;
                    } elseif (
                        $guessResult[0] == $result[0] &&
                        $guessResult[1] == $result[1]
                    ) {
                        $score += $this->calculateGuessScore($rating, $level);
                    }
                }
            }

            $games_right = $gamesRegistry;
        }

        $this->score = $score;
        $this->save();
    }
}
