@use('Illuminate\Support\Facades\Auth')
@php
    if(!isset($adhesion_valid)){
        $adhesion_valid=false;
    }
    $disconectedPages=[
        "Accueil"=>route('accueil'),
        "Règlement intérieur"=>route('reglement_interieur'),
        "Bureau"=>route('bureau'),
    ];
    $nonAdherentsPages=[
        "Mon adhésion"=> route('adhesion'),
    ];
    $userPages=[
        "Mes achats"=>route('achats'),
        "Demande de subvention sportive"=>route('demande-subvention.index'),
        "Nous contacter"=>"/contact",
    ];
    $adminPages=[
        "Adhérents"=>route('adherents'),
        "Configuration des boutiques"=>route('boutiques'),
        "Demandes de subvention sportive"=>route('subventions.index'),
        "Communication"=>route('communiquer'),
    ];
    $current_user = Auth::user();
        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }
@endphp

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <link rel="icon" href="{{ asset("images/icon.png")}}">
        <link rel="stylesheet" href="{{ asset("css/style.css")}}">
        <link rel="stylesheet" href="{{ asset("css/bootstrap.css")}}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="{{ asset('js/menu.js') }}"></script>
        @yield('head')
    </head>

    <body>
        <section id="header">
            <div id="logo" class="dropdown">
                <!--menu-->
                <button id="menu-toggle" class="dropbtn">
                    <div class="menu-icon">
                        <i class="fa-solid fa-window-minimize bar"></i>
                        <i class="fa-solid fa-window-minimize bar"></i>
                        <i class="fa-solid fa-window-minimize bar"></i>
                    </div>
                </button>
            </div>
            <a href="{{ route("accueil") }}" id="logo">
                <img src="{{ asset('images/icon.png') }}" alt="Adolices Logo" loading="lazy">
            </a>
            <a id="names" href="{{ isset($current_user) ? route('accueil') : route('login') }}">
                @if(isset($current_user))
                    <p>{{ $current_user->first_name }}</p>
                    <p>{{ $current_user->last_name }}</p>
                @else
                    <p id="connectionButton">Se connecter</p>
                @endif
            </a>
            <bouton class="userSpace" id="userSpace">
                @if(isset($current_user))
                    @if (isset($current_user->photo) && file_exists(public_path($current_user->photo)))
                        <img id="userImage" src="{{ asset($current_user->photo) }}" alt="Photo de Profil Utilisateur">
                    @else
                        <img id="userImage" src="{{ asset('images/UserPhotos/DefaultUser.png') }}" alt="Photo de Profil par Défaut">
                    @endif
                @else
                    <i class="fa-solid fa-user-slash" id="disconected-user-image"></i>
                @endif
            </bouton>

            <div id="title">
                <h1>Adolices</h1>
            </div>
        </section>
        <div id="userMenu">
            @if(isset($current_user))
                <a href="{{ route('profile') }}">Mon profil</a>
                <a href="{{ route('logout') }}">Se déconnecter</a>
            @else
                <a href="{{ route('login') }}">Se connecter</a>
            @endif
        </div>
        <div id="menu-container">
            @php
                $currentTitle = trim(View::yieldContent('title') ?? '');
            @endphp
            @if (isset($current_user))
                @foreach ($disconectedPages as $page_title => $link )
                    <a href={{ $link }} {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
                @endforeach
                @foreach ($nonAdherentsPages as $page_title => $link )
                    <a href={{ $link }} {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
                @endforeach
                @if($adhesion_valid)
                    @foreach ($userPages as $page_title => $link )
                        <a href={{ $link }} {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
                    @endforeach
                    @if ($current_user->is_admin??false)
                        <div id="Separator"></div>
                        <p id="SeparatorText">Pages d'administration</p>
                        @foreach ($adminPages as $page_title => $link )
                            <a href={{ $link }} class="admin_page" {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
                        @endforeach
                    @endif
                @endif
            @else
                @foreach ($disconectedPages as $page_title => $link )
                    <a href={{ $link }} {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
                @endforeach
                <div id="Separator"></div>
                <a href="{{ route('login') }}">
                    <p id="disconected">Se connecter</p>
                </a>
            @endif

        </div>
        <div id="menu-overlay"></div>
        <div id="user-overlay"></div>
        <section id="content">
            @if (!(isset($admin_page)&&$admin_page))
                <img id=background_image src="{{asset('images/background_image.avif')}}" alt="">
            @endif

            @yield('content')
        </section>
        <section id="footer">

        </section>
        @yield('scripts')
    </body>

</html>
