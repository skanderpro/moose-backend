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
        'title',
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

        return $json[0] ?? [];
    }

    public static function getForUser(Season $season, User $user)
    {
        return static::where('season_id', $season->id)->where('user_id', $user->id)->orderBy('id', 'desc')->first();
    }

    public static function getListForUser(Season $season, User $user)
    {
        return static::where('season_id', $season->id)->where('user_id', $user->id)->get();
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
        $games = $season->games->groupBy('group');
        $teamMapper = fn($game) => [Team::findForSeason($game->first_team_id, $season), Team::findForSeason($game->second_team_id, $season)];

        $games_left = array_merge(
            array_values(empty($games['group_a']) ? [] : $games['group_a']->sortBy('sort_index')->map($teamMapper)->toArray()),
            array_values(empty($games['group_b']) ? [] : $games['group_b']->sortBy('sort_index')->map($teamMapper)->toArray())
        );
        $games_right = array_merge(
            array_values(empty($games['group_c']) ? [] : $games['group_c']->sortBy('sort_index')->map($teamMapper)->toArray()),
            array_values(empty($games['group_d']) ? [] : $games['group_d']->sortBy('sort_index')->map($teamMapper)->toArray())
        );


        $gamesRegistry = [
            $games_left,
        ];
        $guessGamesRegistry = [
            $games_left,
        ];

        // left games
        foreach ($left as $level => $levelGames) {
            if (empty($gamesRegistry[$level + 1])) {
                $gamesRegistry[$level + 1] = [];
            }

            if (empty($guessGamesRegistry[$level + 1])) {
                $guessGamesRegistry[$level + 1] = [];
            }

            foreach ($levelGames as $resultIndex => $result) {
                $guessResult = $guessLeft[$level][$resultIndex] ?? [null, null];
                $teams = $gamesRegistry[$level][$resultIndex];
                $guessTeams = $guessGamesRegistry[$level][$resultIndex];
                $winner = $result[0] > $result[1] ? $teams[0] : $teams[1];
                $guessWinner = $guessResult[0] > $guessResult[1] ? $guessTeams[0] : $guessTeams[1];
                $rating = $winner->rating;
                $lastItemIndex = count($gamesRegistry[$level + 1]) - 1;

                if ($lastItemIndex > -1 && count($gamesRegistry[$level + 1][$lastItemIndex]) == 1) {
                    $gamesRegistry[$level + 1][$lastItemIndex][] = $winner;
                } else {
                    $gamesRegistry[$level + 1][] = [$winner];
                }

                $lastGuessItemIndex = count($guessGamesRegistry[$level + 1]) - 1;

                if ($lastGuessItemIndex > -1 && count($guessGamesRegistry[$level + 1][$lastGuessItemIndex]) == 1) {
                    $guessGamesRegistry[$level + 1][$lastGuessItemIndex][] = $guessWinner;
                } else {
                    $guessGamesRegistry[$level + 1][] = [$guessWinner];
                }

                if (
                    $guessResult[0] === null ||
                    $guessResult[1] === null ||
                    $result[0] === null ||
                    $result[1] === null ||
                    $winner->id !== $guessWinner->id
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

        $leftTeamTree = $gamesRegistry;
        $leftGuessTeamTree = $guessGamesRegistry;

        $gamesRegistry = [
            $games_right,
        ];
        $guessGamesRegistry = [
            $games_right,
        ];

        // right games
        foreach ($right as $level => $levelGames) {
            if (empty($gamesRegistry[$level + 1])) {
                $gamesRegistry[$level + 1] = [];
            }

            if (empty($guessGamesRegistry[$level + 1])) {
                $guessGamesRegistry[$level + 1] = [];
            }

            foreach ($levelGames as $resultIndex => $result) {
                $guessResult = $guessRight[$level][$resultIndex] ?? [null, null];
                $teams = $gamesRegistry[$level][$resultIndex];
                $guessTeams = $guessGamesRegistry[$level][$resultIndex];
                $winner = $result[0] > $result[1] ? $teams[0] : $teams[1];
                $guessWinner = $guessResult[0] > $guessResult[1] ? $guessTeams[0] : $guessTeams[1];
                $rating = $winner->rating;
                $lastItemIndex = count($gamesRegistry[$level + 1]) - 1;

                if ($lastItemIndex > -1 && count($gamesRegistry[$level + 1][$lastItemIndex]) == 1) {
                    $gamesRegistry[$level + 1][$lastItemIndex][] = $winner;
                } else {
                    $gamesRegistry[$level + 1][] = [$winner];
                }

                $lastGuessItemIndex = count($guessGamesRegistry[$level + 1]) - 1;

                if ($lastGuessItemIndex > -1 && count($guessGamesRegistry[$level + 1][$lastGuessItemIndex]) == 1) {
                    $guessGamesRegistry[$level + 1][$lastGuessItemIndex][] = $guessWinner;
                } else {
                    $guessGamesRegistry[$level + 1][] = [$guessWinner];
                }

                if (
                    $guessResult[0] === null ||
                    $guessResult[1] === null ||
                    $result[0] === null ||
                    $result[1] === null ||
                    $winner->id !== $guessWinner->id
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

        $rightTeamTree = $gamesRegistry;
        $rightGuessTeamTree = $guessGamesRegistry;

        // check final
        if (
            // check fact
            !empty($leftTeamTree[5][0][0]) &&
            !empty($rightTeamTree[5][0][0]) &&
            // check guess
            !empty($leftGuessTeamTree[5][0][0]) &&
            !empty($rightGuessTeamTree[5][0][0]) &&
            // check winners
            $leftTeamTree[5][0][0]->id == $leftGuessTeamTree[5][0][0]->id &&
            $rightTeamTree[5][0][0]->id == $rightGuessTeamTree[5][0][0]->id &&
            // check results
            isset($final[0][0][0]) &&
            isset($final[0][0][1]) &&
            isset($guessFinal[0][0][0]) &&
            isset($guessFinal[0][0][1]) &&
            $final[0][0][0] == $guessFinal[0][0][0] &&
            $final[0][0][1] == $guessFinal[0][0][1]
        ) {
            $finalWinner = $final[0][0][0] > $final[0][0][1] ? $leftTeamTree[5][0][0]->rating : $rightTeamTree[5][0][0]->rating;

            $score += $this->calculateGuessScore($finalWinner, 5);
        }

        $this->score = $score;
        $this->save();
    }

    public static function removeSeason(Season $season)
    {
        return static::where('season_id', $season->id)->delete();
    }
}
