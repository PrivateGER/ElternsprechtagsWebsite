@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    Willkommen zurück zu {{ getenv("APP_NAME") }}, {{ \Illuminate\Support\Facades\Auth::user()["name"]  }}!

                    @if (\Illuminate\Support\Facades\Auth::user()["lehrer"] === 1)
                        <p>Sie sind als Lehrer angemeldet.</p>
                     @else
                        <p>Sie sind als Schüler angemeldet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@if (\Illuminate\Support\Facades\Auth::user()["lehrer"] === 0)
    <br />
    @include("layouts.lehrer_search")
@endif
@endsection
