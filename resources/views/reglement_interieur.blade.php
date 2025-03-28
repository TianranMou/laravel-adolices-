@extends('template')

@section('title')
    Règlement intérieur
@endsection

@section('head')
    <link rel="stylesheet" href="{{asset('css/pages_css/reglement_interieur.css')}}">
@endsection

@section('content')
    <section id="reglement_title">
        <h3>Règlement Intérieur de l'Association ADOLICES</h3>
    </section>
    <div id="reglement-container">


        <section id="reglement_content">
            {!! $reglement !!}
        </section>
    </div>
@endsection
