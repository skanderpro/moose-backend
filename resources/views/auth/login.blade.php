@extends('layouts.app')

@section('content')
    <main>
        <div class="container-center">
            <div class="container-center__inner">
                <h1 class="g-title">Login</h1>
                <div class="form">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-item">
                            <input class="g-input" type="text" placeholder="Name" value="{{ old('email') }}" name="email" />
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-item">
                            <input
                                class="g-input"
                                type="password"
                                placeholder="Password"
                                name="password"
                                required autocomplete="current-password"
                            />
                        </div>
                        <button class="g-button">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
