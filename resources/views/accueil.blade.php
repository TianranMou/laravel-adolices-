@extends('template')

@section('title')
    Accueil
@endsection

@section('head')
    <link rel="stylesheet" href="{{asset('css/pages_css/accueil.css')}}">
@endsection

@section('content')
    <div id="acceuil-container"><!-- Titres -->
        <h3 id="accueil_title">Bienvenue sur le site d'Adolices</h3>
        @if (!$adhesion_valid&&$current_user)
            <section id="state_adhesion">
                <h4 id="not_valid">Votre adhésion n'est pas valide</h4>
                <p>Pour adhérer ou renouveler votre adhésion, cliquez sur ce lien :</p>
                <a href="{{ route('adhesion') }}">Page d'adhésion</a>
            </section>
            <br>
        @endif
        <br>
        <section class="shops">
            @if(!empty($shops))
                <!-- boutiques temporaires -->
                <div id="activites">
                    <h3 id="shops_title">Événements/Sorties</h3>
                    <div class="shops-cards">
                        @foreach ($shops as $shop)
                            <div class="boutique-card">
                                @if(file_exists($shop->thumbnail))
                                    <img src="{{ asset($shop->thumbnail) }}" alt="{{ $shop->shop_name }}" class="boutique-image">
                                @else
                                    <img src="{{asset('images/shop/base_shop_image.jpg')}}" alt="{{ $shop->shop_name }}" class="boutique-image">
                                @endif
                                <h4 class="boutique-title">{{ $shop->shop_name }}</h4>
                                <p class="boutique-description">{{ $shop->short_description }}</p>
                                @if ($current_user)
                                    <a href="{{ "/shop/".$shop->shop_id }}" class="boutique-link">En savoir plus</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($billeteries))
                <!-- boutiques permanentes -->
                <br>
                @if(!empty($shops))
                    <div id="billeteries">
                @else
                    <div id="activites">
                @endif
                    <h3 id="shops_title">Billetteries</h3>
                    <div class="shops-cards">
                        @foreach ($billeteries as $shop)
                            <div class="boutique-card">
                                @if(file_exists($shop->thumbnail))
                                    <img src="{{ asset($shop->thumbnail) }}" alt="{{ $shop->shop_name }}" class="boutique-image">
                                @else
                                    <img src="{{asset('images/shop/base_shop_image.jpg')}}" alt="{{ $shop->shop_name }}" class="boutique-image">
                                @endif
                                <h4 class="boutique-title">{{ $shop->shop_name }}</h4>
                                <p class="boutique-description">{{ $shop->short_description }}</p>
                                @if ($current_user)
                                    <a href="{{ "/shop/".$shop->shop_id }}" class="boutique-link">En savoir plus</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
        @if ($presentation)<!-- présentation de l'Association -->
            <section class="card">
                <div class="card-body prose max-w-none">
                    {!! $presentation !!}
                </div>
            </section>
        @endif

    </div>
@endsection
