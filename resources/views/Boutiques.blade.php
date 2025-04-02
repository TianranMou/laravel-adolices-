@extends('template')

@section('title')
    Boutiques
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap5.min.css') }}">
@endsection

@php
    $admin_page = true;
@endphp

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="text-center">Liste des Boutiques</h3>
            </div>
            <div class="col-md-6 text-end">
                <a class="btn btn-primary" id="addBoutique" href="ajouter-produit/">
                    <i class="fa fa-plus"></i> Ajouter une boutique
                </a>
            </div>
        </div>

        <table id="boutiquesTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description courte</th>
                    <th>Description longue</th>
                    <th>Bon de commande</th>
                    <th>Lien helloasso</th>
                    <th>Lien document</th>
                    <th>Date de fin</th>
                    <th>Lien photo</th>
                    <th>Miniature</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($boutiques as $boutique)
                    <tr>
                        <td>{{ $boutique->shop_name }}</td>
                        <td>{{ $boutique->short_description }}</td>
                        <td>{{ $boutique->long_description }}</td>
                        <td>
                            @if($boutique->bc_link)
                                <a href="{{ $boutique->bc_link }}" target="_blank">Voir</a>
                            @else
                                Aucun lien
                            @endif
                        </td>
                        <td>
                            @if($boutique->ha_link)
                                <a href="{{ $boutique->ha_link }}" target="_blank">Voir</a>
                            @else
                                Aucun lien
                            @endif
                        </td>
                        <td>
                            @if ($boutique->doc_link)
                                <a href="{{ asset($boutique->doc_link) }}" target="_blank">Voir</a>
                            @else
                                Aucun lien
                            @endif
                        </td>
                        <td>{{ $boutique->end_date ?? 'Permanente' }}</td>
                        <td>
                            @if(file_exists($boutique->photo_link))
                                <a href="{{ asset($boutique->photo_link) }}" target="_blank">Voir</a>
                            @else
                                Aucune photo
                            @endif
                        </td>
                        <td>
                            @if(file_exists($boutique->thumbnail))
                                <img src="{{ asset($boutique->thumbnail) }}" alt="photo" style="width: 100px; height: 100px;">
                            @else
                                Aucune photo
                            @endif
                        </td>
                        <td>
                            {{($boutique->is_active&&($boutique->end_date>=date('Y-m-d')||$boutique->end_date==null))?'Oui':'Non'}}
                        </td>
                        <td>
                            @if($BoutiquesGeredByUser->contains($boutique->shop_id)||env('APP_DEBUG'))
                                <a href="{{ "ajouter-produit/".$boutique->shop_id }}" class="btn btn-primary editBoutique" data-id="{{ $boutique->shop_id }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br>
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        window.initialBoutiques = @json($boutiques);
    </script>
    <script src="{{ asset('js/pages_js/Boutiques.js') }}"></script>
@endsection
