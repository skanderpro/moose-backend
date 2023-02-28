<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LeaderboardController extends Controller
{
    public function index()
    {
        $users = User::where('score', '>', 0)->orderBy('score', 'desc')->get();

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
            'users' => $users,
            'avatars' => $avatars,
        ]);
    }
}
