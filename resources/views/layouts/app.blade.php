<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter&family=Oswald:wght@700&family=Unbounded:wght@400;500;700&display=swap&_v=20230212010729"
        rel="stylesheet"
    />
    <link rel="stylesheet" href="/html/dist/files/css/normalize.css?_v=20230212010729" />
    <link rel="stylesheet" href="/html/dist/css/style.min.css?_v=20230212010729" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

</head>
<body>
<div class="main-wrapper">
    <header class="header" style="background-image: url(/html/dist/img/header-bg.jpg)">
        <div class="container">
            <div class="header__inner">
                <div class="header__inner-logo">
                    <a href="/"><img src="/html/dist/img/logo.svg" alt="" /></a>
                </div>

                <nav class="header__inner-nav">
                    <ul>
                        @guest
                            <li><a href="{{ route('leaderboard') }}">LEADERS</a></li>
                            <li><a href="{{ route('login') }}">LOGIN</a></li>
                            <li>
                                <a class="nav-btn-registration" href="{{ route('register') }}">REGISTRATION</a>
                            </li>
                        @else
                            <li><a href="{{ route('scores') }}">Scores</a></li>
                            <li><a href="{{ route('leaderboard') }}">LEADERS</a></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="nav-btn-registration">LOG OUT</button>
                                </form>
                            </li>
                        @endguest

                    </ul>
                </nav>
                <div class="header__inner-burger-btn">
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    @yield('content')
</div>
<footer class="footer">
    <div class="container">
        <div class="footer__inner">
            <div class="footer__inner-text">moosehoops.com {{ NOW()->format('Y') }}</div>
        </div>
    </div>
</footer>
<script src="/html/dist/js/app.min.js?_v=20230212010729"></script>
</body>
</html>
