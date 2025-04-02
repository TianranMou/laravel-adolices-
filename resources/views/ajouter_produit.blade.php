@extends('template')

@section('title')
    Ajouter un Produit
@endsection

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('head')
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap5.min.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>

    <script src="{{ asset('js/pages_js/Product.js') }}"></script> 
@endsection


@section('content')

    

    <!-- Bouton pour ouvrir le modal -->
    
    <!--<button class="btn btn-primary" id="addProductBtn">
        <i class="fa fa-plus"></i> Ajouter un produit
    </button>
-->
    <!-- Modal d'ajout de produit -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="addProductModal">Ajouter un produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" action="{{ route('produits.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Nom du produit</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="withdrawal_method" class="form-label">Méthode de retrait</label>
                            <select class="form-control" id="withdrawal_method" name="withdrawal_method" required>
                                <option value="pickup">Pickup</option>
                                <option value="delivery">Delivery</option>
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
                        <!-- Si tu veux les inclure dans le formulaire, tu peux les ajouter, mais ils sont fixés à 1 dans ton contrôleur -->
                        <input type="hidden" name="shop_id" value="1">
                        <input type="hidden" name="quota_id" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="saveProduct">Ajouter</button>
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
                    <!-- <td>{{ $product->product_id }}</td> -->
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->subsidized_price }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->withdrawal_method }}</td>
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
                                    window.location.href = `/choisir-type-ticket/${productId}`;
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

