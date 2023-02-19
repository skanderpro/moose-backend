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

    public function games()
    {
        return $this->hasMany(Game::class)->orderBy('id', 'asc');
    }

    public static function getActive()
    {
        return static::where('is_active', true)->orderBy('start', 'desc')->first();
    }

    public function getResults()
    {
        $json = json_decode($this->results, true);
        if (!is_array($json)) {
            $json = [
                'left' => [],
                'right' => [],
                'final' => [],
            ];
        }

        return $json;
    }
}
