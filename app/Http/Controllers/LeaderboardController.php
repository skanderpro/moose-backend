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
        $users = User::where('score', '>', 0)->get();

        return view('leaderboard', [
            'users' => $users,
        ]);
    }
}
