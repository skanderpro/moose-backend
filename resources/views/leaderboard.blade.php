@extends('layouts.app')

@section('content')
    <main>
        <div class="leaderboard">
            <div class="container">
                <div class="leaderboard__inner">
                    <div class="g-title">Leaderboard</div>
                    <div class="leaderboard__inner-list">
                        <table>
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Score</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($guesses as $guessArr)
                            @foreach($guessArr as $index => $guess)

                                <tr>
                                    <td>
                                        <img
                                            class="leaderboard__inner-list-img"
                                            src="https://api.dicebear.com/5.x/bottts-neutral/svg?seed={{ array_rand($avatars) }}"
                                            alt=""
                                        />
                                    {{ $guess->user->name }} / Variant # {{ $index + 1 }}
                                    </td>
                                    <td>{{ $guess->score }}</td>
                                </tr>
                            @endforeach
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
