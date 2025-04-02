@extends('template')

@section('title')
    {{ $shop->shop_name }}
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/shop.css') }}">
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

        <h2 class="products-title">Produits disponibles</h2>

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
                                <span class="label">Prix normal:</span>
                                <span class="value">{{ $product->price }} €</span>
                            </div>
                            <div class="subsidized-price">
                                <span class="label">Prix subventionné:</span>
                                <span class="value">{{ $product->subsidized_price }} €</span>
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
