@extends('layouts.app')

@section('content')
    <main>
        <div class="container-center">
            <div class="container-center__inner">
                <h1 class="g-title">Registration</h1>
                <div class="form">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-item">
                            <input class="g-input" type="text" placeholder="Name" name="name" value="{{ old('name') }}"
                                   required autocomplete="name" autofocus/>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-item">
                            <input class="g-input" type="email" placeholder="Email" name="email"
                                   value="{{ old('email') }}" required autocomplete="email"/>

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
                                name="password" required autocomplete="new-password"
                            />
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-item">
                            <input
                                class="g-input"
                                type="password"
                                placeholder="Repeat Password"
                                name="password_confirmation" required autocomplete="new-password"
                            />
                        </div>
                        <button class="g-button">Registration</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
