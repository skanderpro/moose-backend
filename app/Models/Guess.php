<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guess extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'user_id',
        'results_left',
        'results_right',
        'results_final',
    ];

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
}
