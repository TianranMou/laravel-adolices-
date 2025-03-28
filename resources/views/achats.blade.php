@extends('template')

@section('title')
    Achats
@endsection

@section('head')
@endsection

@section('content')
    <div class="container mt-10">
        <section class="card mb-8">
            <div class="card-body text-center">
                <h1 class="text-3xl mb-4">Boutique ADOLICES</h1>
                <p class="text-lg text-gray-600">Découvrez nos offres exclusives pour les adhérents.</p>
                @if (!$adhesion_valid)
                    <div class="alert alert-danger mx-auto mt-4" style="max-width: 500px;">
                        <h4 class="text-xl mb-2">Votre adhésion n'est pas valide</h4>
                        <p>Vous devez être adhérent pour effectuer des achats. Renouvelez votre adhésion ici :</p>
                        <a href="{{ route('adhesion') }}" class="btn btn-secondary mt-2">Page d'adhésion</a>
                    </div>
                @endif
            </div>
        </section>

        @if ($adhesion_valid)
            <section class="mb-8">
                <h2 class="text-2xl text-center mb-6">Articles disponibles</h2>
                @if (empty($items))
                    <p class="text-center text-gray-600">Aucun article disponible pour le moment.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($items as $item)
                            <div class="card">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-48 object-cover rounded-t-lg">
                                <div class="p-4">
                                    <h3 class="text-xl mb-2">{{ $item['name'] }}</h3>
                                    <p class="text-gray-600 mb-2">{{ $item['description'] }}</p>
                                    <p class="text-lg font-bold text-primary mb-4">{{ $item['price'] }} €</p>
                                    <form action="{{ route('cart.add', $item['id']) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-full">Ajouter au panier</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card">
                <div class="card-body text-center">
                    <h2 class="text-2xl mb-4">Votre panier</h2>
                    @if (empty($cart))
                        <p class="text-gray-600">Votre panier est vide.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($cart as $cartItem)
                                <div class="flex justify-between items-center p-2 bg-gray-100 rounded">
                                    <span>{{ $cartItem['name'] }} - {{ $cartItem['price'] }} €</span>
                                    <form action="{{ route('cart.remove', $cartItem['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Retirer</button>
                                    </form>
                                </div>
                            @endforeach
                            <p class="text-lg font-bold mt-4">Total : {{ $cartTotal }} €</p>
                            <a href="{{ route('checkout') }}" class="btn btn-secondary mt-4">Passer au paiement</a>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </div>
@endsection