<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_team_id',
        'second_team_id',
        'season_id',
        'type',
    ];

    public function firstTeam()
    {
        return $this->belongsTo(Team::class, 'first_team_id');
    }

    public function secondTeam()
    {
        return $this->belongsTo(Team::class, 'second_team_id');
    }
}
