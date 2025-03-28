
@extends('template')

@section('title')
    Erreur
@endsection

@section('head')
    <link rel="stylesheet" href="{{asset('css/pages_css/error.css')}}">
@endsection

@section('content')
    <div id="error-container">
        <img src="{{ asset("images/icon.png")}}" alt="Logo Adolices">
        <h1 class="error-title">Oops !</h1>
        <p class="error-message">La page que vous recherchez n'existe pas.</p>

        <a href="/" class="error-button">Retour à l'accueil</a>
    </div>
@endsection
