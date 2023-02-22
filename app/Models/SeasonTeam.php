<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'season_id',
        'group',
        'rating',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
