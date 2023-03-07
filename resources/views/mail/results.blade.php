@extends('mail.layout.main')

@section('content')
    <h1 style="font-weight: 700;font-size: 35px;line-height: 40px;text-align: center;color: #0d81fc;font-family: Unbounded,sans-serif;">Your Scores</h1>
    <ul>
        @foreach($user->guesses as $index => $guess)
            <li style="text-align: center;color: #28446d;">Variant #{{ $index + 1 }} - <b>{{ $guess->score }}</b></li>
        @endforeach
    </ul>
@endsection
