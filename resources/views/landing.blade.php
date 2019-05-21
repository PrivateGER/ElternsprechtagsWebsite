@extends('layouts.app')
@section('content')
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 70vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        @media (min-width:1025px) {
            .title {
                font-size: 84px;
            }
        }
    </style>
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="m-b-md">
		<img src="gymnasium_st_michael.jpg" heigth="270" width="250" />
                <h1 class="title">{{ getenv("APP_NAME") }}</h1>
            </div>

            <div class="links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
@endsection