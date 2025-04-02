@extends('template')

@section('title')
    Contact
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/pages_css/contact.css') }}">
    <script src="{{ asset('js/pages_js/contact.js') }}"></script>
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h3 mb-0">Contact</h1>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="shop-select" class="form-label">Sélectionnez une boutique</label>
                            <select class="form-select" id="shop-select">
                                <option value="" selected>Choisir une boutique</option>
                                <option value="all">Contact général (tous les gestionnaires)</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->shop_id }}">{{ $shop->shop_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="administrators-container" class="mt-4 d-none">
                            <h4 class="mb-3" id="admin-title">Gestionnaires de la boutique</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Email</th>
                                            <th id="shop-column-header" class="d-none">Boutique</th>
                                        </tr>
                                    </thead>
                                    <tbody id="administrators-list">
                                        <!-- Les gestionnaires seront affichés ici -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="no-admins-message" class="alert alert-info d-none">
                                Aucun administrateur trouvé pour cette boutique.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="social-links text-center">
                            <h5>Retrouvez-nous également sur</h5>
                            <div class="d-flex justify-content-center gap-4 mt-3">
                                <a href="https://www.facebook.com/groups/adolices" target="_blank" class="btn btn-outline-primary">
                                    <i class="fab fa-facebook-f me-2"></i>Facebook
                                </a>
                                <a href="https://www.helloasso.com/associations/adolices" target="_blank" class="btn btn-outline-success">
                                    <i class="fas fa-credit-card me-2"></i>HelloAsso
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
