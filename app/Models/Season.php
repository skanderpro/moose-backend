<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start',
        'is_active',
        'results_left',
        'results_right',
        'results_final',
    ];

    public function guesses()
    {
        return $this->hasMany(Guess::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class)->orderBy('id', 'asc');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'season_teams');
    }

    public static function getActive()
    {
        return static::where('is_active', true)->orderBy('start', 'desc')->first();
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

    public function meta()
    {
        return $this->hasMany(SeasonTeam::class)->with('team');
    }
}
