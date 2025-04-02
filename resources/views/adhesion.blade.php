@extends('template')

@section('title')
    Mon adhésion
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset("css/pages_css/adhesion.css")}}">
@endsection

@section('content')
    <section id="adhesion_title">
            <h3>Adhésion</h3>
    </section>
    <div id="adhesion-container">
        <br>
        <section id="state_adhesion">
            @if ($adhesion_valid)
                <p id="valid">Votre adhésion est <strong>Valide</strong></p>
            @else
                <h4 id="not_valid">Votre adhésion n'est pas valide</h4>
                <p>Pour adhérer ou renouveler votre adhésion, cliquez sur ce lien :</p>
                <a href="{{ $adhesion_link }}">Lien d'adhésion</a>
            @endif
        </section>
    </div>
@endsection
