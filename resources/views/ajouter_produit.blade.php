@extends('template')

@php
    $admin_page = true;
@endphp

@section('title')
    Ajouter un Produit
@endsection

@php
    $admin_page = true;
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('head')
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages_css/Product.css') }}">

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages_js/Product.js') }}"></script>
@endsection

<div id="success-message" class="alert alert-success" style="display: none;"></div>


@section('content')
    <!-- Formulaire pour modifier la boutique -->
    <div class="container mt-4">
        <form action="{{ url('edit-boutique/update/' . $shop_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="md-6">
                <div class="mb-3">
                    <label for="shop_name" class="form-label">Nom de la boutique</label>
                    <input type="text" class="form-control" id="shop_name" name="shop_name" value="{{ old('shop_name', $boutique->shop_name) }}" required>
                </div>
                <div class="mb-3">
                    <label for="short_description" class="form-label">Description courte</label>
                    <input type="text" class="form-control" id="short_description" name="short_description" value="{{ old('short_description', $boutique->short_description) }}" required>
                </div>
                <div class="mb-3">
                    <label for="long_description" class="form-label">Description longue</label>
                    <textarea class="form-control" id="long_description" name="long_description" required>{{ old('long_description', $boutique->long_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="bc_link" class="form-label">Bon de commande</label>
                    <input type="url" class="form-control" id="bc_link" name="bc_link" value="{{ old('bc_link', $boutique->bc_link) }}">
                </div>
                <div class="mb-3">
                    <label for="ha_link" class="form-label">Lien Helloasso</label>
                    <input type="url" class="form-control" id="ha_link" name="ha_link" value="{{ old('ha_link', $boutique->ha_link) }}">
                </div>
                <div class="mb-3">
                    <label for="doc_link" class="form-label">Lien document</label>
                    <input type="url" class="form-control" id="doc_link" name="doc_link" value="{{ old('doc_link', $boutique->doc_link) }}">
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label">Date de fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $boutique->end_date) }}">
                    <small class="form-text text-muted">Laissez vide si la boutique est permanente.</small>
                </div>
                <div class="mb-3">
                    <label for="photo_link" class="form-label">Photo</label>
                    <input type="file" class="form-control" id="photo_link" name="photo_link" accept="image/*">
                    @if($boutique->photo_link)
                        <img src="{{ asset('storage/'.$boutique->photo_link) }}" alt="Photo actuelle" class="mt-2" width="100">
                    @endif
                </div>

                <div class="mb-3">
                    <label for="thumbnail" class="form-label">Miniature</label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                    @if($boutique->thumbnail)
                        <img src="{{ asset('storage/'.$boutique->thumbnail) }}" alt="Miniature actuelle" class="mt-2" width="100">
                    @endif
                </div>
                <div class="mb-3">
                    <label for="is_active" class="form-label">Active</label>
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $boutique->is_active) ? 'checked' : '' }}>
                </div>
                <div class="md-6">
                    <button type="submit" class="btn btn-primary mt-4">Mettre à jour la boutique</button>
                </div>
            </div>
        </form>
        <div class="container mt-4">
            <form action="{{ route('shops.gestionnaires', ['shop_id' => $shop_id]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="user_ids" class="form-label">Sélectionnez les gestionnaires</label>
                    <select id="user_ids" name="user_ids[]" class="form-select" multiple>
                        @foreach ($users as $user)
                            <option value="{{ $user->user_id }}"
                                {{ $boutique->administrators->contains($user->user_id) ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs gestionnaires.</small>
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour les gestionnaires</button>
            </form>
        </div>
    </div>



    <!-- Modal d'ajout de produit -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="addProductModal">Ajouter un produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" action="{{ route('produits.store', ['shop_id' => $shop_id] )}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Nom du produit</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="withdrawal_method" class="form-label">Méthode de retrait</label>
                            <select class="form-control" id="withdrawal_method" name="withdrawal_method" required>
                                <option value="pickup">Retirer</option>
                                <option value="delivery">Livraison</option>
                                <option value="digital">Digital</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subsidized_price" class="form-label">Prix subventionné</label>
                            <input type="number" class="form-control" id="subsidized_price" name="subsidized_price" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Prix</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="dematerialized" class="form-label">Produit dématérialisé</label>
                            <input type="checkbox" class="form-check-input" id="dematerialized" name="dematerialized" value="1">
                        </div>
                        <!-- Les champs shop_id et quota_id sont hardcodés dans le contrôleur -->
                        <input type="hidden" name="shop_id" value="{{ $shop_id }}">
                        <input type="hidden" name="quota_id" value="1"><!-- Remplacez par la valeur réelle de quota_id -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="saveProduct">Ajouter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification de produit -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="editProductModal">Modifier un produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" action="{{ route('produit.update', ['shop_id' => $shop_id]) }}" method="POST">
                        @csrf
                        <input type="hidden" id="edit_product_id" name="product_id"> <!-- Champ caché pour stocker l'ID -->
                        <div class="mb-3">
                            <label for="edit_product_name" class="form-label">Nom du produit</label>
                            <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_withdrawal_method" class="form-label">Méthode de retrait</label>
                            <select class="form-control" id="edit_withdrawal_method" name="withdrawal_method" required>
                                <option value="pickup">Retirer</option>
                                <option value="delivery">Livraison</option>
                                <option value="digital">Digital</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_subsidized_price" class="form-label">Prix subventionné</label>
                            <input type="number" class="form-control" id="edit_subsidized_price" name="subsidized_price" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Prix</label>
                            <input type="number" class="form-control" id="edit_price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_dematerialized" class="form-label">Produit dématérialisé</label>
                            <input type="checkbox" class="form-check-input" id="edit_dematerialized" name="dematerialized" value="1">
                        </div>
                        <input type="hidden" name="shop_id" value="{{ $shop_id }}">
                        <input type="hidden" name="edit_quota_id" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="updateProduct">Modifier</button>
                </div>
            </div>
        </div>
    </div>


    <table id="productsTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Nom du produit</th>
                <th>Prix adhérent</th>
                <th>Prix non adhérent</th>
                <th>Méthode de retrait</th>
                <th>Catégorie</th>
                <th>Nombre de tickets</th>
                <th>Actions</th>

            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->subsidized_price }}</td>
                    <td>{{ $product->price }}</td>
                    <td>
                        @if ($product->withdrawal_method == 'pickup')
                            Retirer
                        @elseif ($product->withdrawal_method == 'delivery')
                            Livraison
                        @elseif ($product->withdrawal_method == 'digital')
                            Digital
                        @endif
                    </td>

                    <td>{{ $product->dematerialized === true ? 'dématérialisé' : 'physique'  }}</td>
                    <td>{{ $product->nbTickets }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-product" data-id="{{ $product->product_id }}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-primary gestion-ticket" data-id="{{ $product->product_id }}">
                            <i class="fa fa-plus"></i>
                        </button>
                        <script>
                            $(document).ready(function () {
                                $('.gestion-ticket').click(function () {
                                    let productId = $(this).data('id');
                                    window.location.href = `/ajouter-ticket/${productId}`;
                                });
                            });
                        </script>
                        <button class="btn btn-sm btn-danger delete-product" data-id="{{ $product->product_id }}">
                            <i class="fa fa-trash"></i>
                        </button>

                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

