<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guess;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LeaderboardController extends Controller
{
    public function index()
    {
        $guesses = Guess::where('score', '>', 0)->with('user')->orderBy('score', 'desc')->get()->groupBy('user_id');

        $avatars = [
            'Leo',
            'Max',
            'Harley',
            'Willow',
            'Precious',
            'Zoey',
            'Zoe',
            'Missy',
            'Pumpkin',
            'Princess',
            'Mia',
            'Felix',
            'Baby',
            'Gracie',
        ];

        return view('leaderboard', [
            'guesses' => $guesses,
            'avatars' => $avatars,
        ]);
    }
}
