@extends('template')

@section('title')
    Erreur
@endsection

@section('content')
    <div class="container my-5 text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <img src="{{ asset("images/icon.png")}}" alt="Logo Adolices" class="img-fluid mb-4" style="max-width: 150px;">
                        <div class="mb-4">
                            <i class="fa-solid fa-triangle-exclamation fa-3x text-warning"></i>
                        </div>
                        <p class="lead mb-4">La page que vous recherchez n'existe pas.</p>
                        <a href="{{ route('accueil') }}" class="btn btn-primary">
                            <i class="fa-solid fa-home me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
