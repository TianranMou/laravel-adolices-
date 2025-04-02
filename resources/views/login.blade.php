@extends('template')

@section('title')
    Se connecter
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/login.css') }}">
@endsection

@section('content')
    <h3 id="login-title">Se connecter</h3>
    <div id="login-container">
        @if ($errors->any())
            <div class="alert alert-dismissible alert-danger" id="error">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/login" class="form-floating mb-3">
            @csrf

            <div class="mb-3 form-floating">
                <input type="email" name="email" id="email" placeholder="Email" class="form-control" required>
                <label for="email">Email</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="password" placeholder="Mot de passe" name="password" id="password" class="form-control" autocomplete="current-password" required>
                <label for="password" >Mot de passe</label>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </div>
        </form>
        <a href="{{route('register')}}">Créer un compte</a>
        <div class="mt-3">
            <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
        </div>
    </div>
@endsection
