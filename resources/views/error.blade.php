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
        <h1 class="error-title">Oops ! Erreur {{ $errorId }}</h1>
        @if ($errorId==418)
            <i class="fa-solid fa-mug-hot"></i>
        @endif
        <p class="error-message">{{ $errorMessage }}</p>

        <a href="/" class="error-button">Retour à l'accueil</a>
    </div>
@endsection
