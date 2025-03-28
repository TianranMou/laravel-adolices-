@extends('template')

@section('title')
    Boutique ADOLICES
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/achats.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endsection

@section('content')
    <div class="container py-5">
        <div class="shop-header text-center mb-5">
            <h1 class="display-4 fw-bold">Boutique ADOLICES</h1>
            <p class="lead text-secondary">Découvrez nos offres exclusives pour les adhérents.</p>
            
            @if (!$adhesion_valid)
                <div class="alert alert-warning mx-auto mt-4" style="max-width: 700px;">
                    <h4 class="alert-heading">Votre adhésion n'est pas valide</h4>
                    <p>Vous devez être adhérent pour effectuer des achats. Renouvelez votre adhésion ici :</p>
                    <a href="{{ route('adhesion') }}" class="btn btn-outline-dark mt-2">Page d'adhésion</a>
                </div>
            @endif
        </div>

        <h2 class="section-title text-center mb-4">Articles disponibles</h2>

        <div class="row g-4">
            @foreach ($items as $item)
                <div class="col-md-4">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="img-fluid rounded">
                        </div>
                        <div class="product-details p-3">
                            <h3 class="product-title">{{ $item['name'] }}</h3>
                            <p class="product-description text-muted">{{ $item['description'] }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="product-price">{{ number_format($item['price'], 2) }} €</span>
                                @if ($adhesion_valid)
                                    <form action="{{ route('cart.add', $item['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if (count($cart) > 0)
            <div class="cart-section mt-5 pt-4 border-top">
                <h2 class="section-title text-center mb-4">Votre panier</h2>
                <div class="cart-content mx-auto" style="max-width: 800px;">
                    @foreach ($cart as $cartItem)
                        <div class="cart-item d-flex justify-content-between align-items-center p-3 mb-3 bg-light rounded">
                            <div>
                                <h5 class="mb-1">{{ $cartItem['name'] }}</h5>
                                <p class="mb-0 text-primary fw-bold">{{ number_format($cartItem['price'], 2) }} €</p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="me-3">Quantité: {{ $cartItem['quantity'] }}</span>
                                <form action="{{ route('cart.remove', $cartItem['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    <div class="cart-total d-flex justify-content-between align-items-center p-3 bg-light rounded">
                        <span class="fw-bold fs-5">Total:</span>
                        <span class="fw-bold fs-5 text-primary">{{ number_format($cartTotal, 2) }} €</span>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('checkout') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Passer au paiement
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-cart text-center mt-5 pt-4 border-top">
                <p class="text-muted">Votre panier est vide.</p>
            </div>
        @endif
    </div>
@endsection