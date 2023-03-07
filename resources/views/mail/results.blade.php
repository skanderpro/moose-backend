@extends('mail.layout.main')

@section('content')
    <h1 style="text-align: center">Your Scores</h1>
    <ul>
        @foreach($user->guesses as $index => $guess)
            <li style="text-align: center">Variant #{{ $index + 1 }} - <b>{{ $guess->score }}</b></li>
        @endforeach
    </ul>
@endsection
