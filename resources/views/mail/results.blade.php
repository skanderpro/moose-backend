@extends('mail.layout.main')

@section('content')
    <h1 style="font-weight: 700;font-size: 35px;line-height: 40px;text-align: center;color: #0d81fc;font-family: Unbounded,sans-serif;">Your Score</h1>
    <ul>
        @foreach($user->guesses as $index => $guess)
            <li style="text-align: center;color: #28446d;">Variant #{{ $index + 1 }} - <b>{{ $guess->score }}</b></li>
        @endforeach
    </ul>
    <div style="margin: 32px auto; text-align: center">
        <a href="{{ url(route('leaderboard', true)) }}" style="background-color: #f7ed1d;padding: 10px;color: #28446d;border-radius: 10px;font-weight: 700;font-size: 14px;line-height: 20px;text-transform: uppercase;transition: all .3s ease;font-family: Unbounded,sans-serif;">Visit the leaderboard</a>
    </div>
@endsection
