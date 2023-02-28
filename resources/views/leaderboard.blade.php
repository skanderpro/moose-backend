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
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <img
                                            class="leaderboard__inner-list-img"
                                            src="https://api.dicebear.com/5.x/bottts-neutral/svg?seed={{ array_rand($avatars) }}"
                                            alt=""
                                        />
                                    {{ $user->name }}
                                    </td>
                                    <td>{{ $user->score }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
