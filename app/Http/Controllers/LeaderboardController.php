<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LeaderboardController extends Controller
{
    public function index()
    {
        return view('leaderboard');
    }
}
