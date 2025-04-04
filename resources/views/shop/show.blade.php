@extends('template')

@section('title')
    {{ $shop->shop_name }}
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages_css/show_shop.css') }}">
    <!-- Add Bootstrap JS first, before your script -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/pages_js/show_shop.js') }}"></script>
@endsection

@section('content')
    <div class="shop-container">
        <div class="shop-header">
            <div class="shop-image">
                @if(file_exists($shop->thumbnail))
                    <img src="{{ asset($shop->thumbnail) }}" alt="{{ $shop->shop_name }}">
                @else
                    <img src="{{ asset('images/shop/base_shop_image.jpg') }}" alt="{{ $shop->shop_name }}">
                @endif
            </div>
            <div class="shop-info">
                <h1>{{ $shop->shop_name }}</h1>
                <p class="shop-description">{{ $shop->short_description }}</p>
                <p class="shop-long-description">{{ $shop->long_description }}</p>

                @if($shop->end_date)
                    <p class="shop-date">Disponible jusqu'au {{ \Carbon\Carbon::parse($shop->end_date)->format('d/m/Y') }}</p>
                @endif

                @if($shop->doc_link)
                    <a href="{{ asset($shop->doc_link) }}" target="_blank" class="btn btn-secondary">Voir le document</a>
                @endif

                @if($shop->ha_link)
                    <a href="{{ $shop->ha_link }}" target="_blank" class="btn btn-primary">Lien HelloAsso</a>
                @endif
            </div>
        </div>

        <h2 class="products-title" id="availables-items">Produits disponibles</h2>

        @if($products->isEmpty())
            <div class="no-products">
                <p>Aucun produit n'est disponible dans cette boutique pour le moment.</p>
            </div>
        @else
            <div class="products-container">
                @foreach($products as $product)
                    <div class="product-card">
                        <h3 class="product-name">{{ $product->product_name }}</h3>
                        <div class="product-prices">
                            <div class="price">
                                <span class="label">Prix normal :</span>
                                <span class="value">{{ $product->price }} €</span>
                            </div>
                            <div class="subsidized-price">
                                <span class="label">Prix subventionné :</span>
                                <span class="value">{{ $product->subsidized_price }} €</span>
                            </div>
                        </div>
                        <div class="product-actions">
                            <button type="button" class="btn btn-primary custom-modal-trigger" value={{ $product->product_id }} style="float: right;">
                                <i class="fas fa-shopping-cart"></i> Acheter
                            </button>
                        </div>
                    </div>

                    <!-- Modal pour l'achat -->
                    <div class="modal fade" id="purchaseModal{{ $product->product_id }}" tabindex="-1" aria-labelledby="purchaseModalLabel{{ $product->product_id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="purchaseModalLabel{{ $product->product_id }}">Acheter {{ $product->product_name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ url('/shop/purchase-ticket/' . $product->product_id) }}" method="POST" id="purchaseForm{{ $product->product_id }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                                        <div class="mb-3">
                                            <label for="regular_quantity{{ $product->product_id }}" class="form-label">Prix normal ({{ $product->price }} €)</label>
                                            <input type="number" class="form-control quantity-input" id="regular_quantity{{ $product->product_id }}"
                                                name="regular_quantity" value="0" min="0">
                                        </div>

                                        <div class="mb-3">
                                            <label for="subsidized_quantity{{ $product->product_id }}" class="form-label">Prix subventionné ({{ $product->subsidized_price }} €)</label>
                                            <input type="number" class="form-control quantity-input" id="subsidized_quantity{{ $product->product_id }}"
                                                name="subsidized_quantity" value="0" min="0" {{ !$adhesion_valid ? 'disabled' : '' }}>
                                            @if(!$adhesion_valid)
                                                <div class="text-danger mt-1">(Adhésion requise pour les prix subventionnés)</div>
                                            @endif
                                        </div>

                                        <div id="validation-message{{ $product->product_id }}" class="text-danger mb-3" style="display: none;">
                                            Veuillez sélectionner au moins un ticket.
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary validate-purchase">Valider l'achat</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="back-link">
            <a href="{{ route('accueil') }}" class="btn btn-outline-primary">Retour à l'accueil</a>
        </div>
    </div>
@endsection
