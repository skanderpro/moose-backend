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
        'results',
    ];

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
