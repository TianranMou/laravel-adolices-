@extends('template')

@section('title')
    Règlement intérieur
@endsection

@section('head')
    <link rel="stylesheet" href="{{asset('css/bootstrap.css')}}">
    <link rel="stylesheet" href="{{ asset('css/pages_css/reglement_interieur.css') }}">
@endsection

@section('content')
    <section id="reglement_title">
        <h3>Règlement intérieur de ADOLICES</h3>
    </section>
    <br>
    <div id="reglement-container">
        @if(isset($pdfExists) && $pdfExists)
            <div class="download-button">
                <a href="{{ route('reglement.download') }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Télécharger le règlement en PDF
                </a>
            </div>
        @endif
    </div>
@endsection
