<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'score',
        'logo',
    ];

    public function seasons()
    {
        return $this->belongsToMany(Season::class, 'season_teams');
    }

    public static function findForSeason($id, Season $season)
    {
        $entity = static::find($id);
        $seasonTema = SeasonTeam::where('season_id', $season->id)->where('team_id', $entity->id)->first();
        $entity->rating = $seasonTema->rating;

        return $entity;
    }
}
