@use('Illuminate\Support\Facades\Auth')
@php
    if(!isset($adhesion_valid)){
        $adhesion_valid=false;
    }
    $disconectedPages=[
        "Accueil"=>"/",
        "Mon Adhésion"=>"/adhesion",
        "Règlement intérieur"=>"/reglement-interieur",
        "Bureau"=>"/bureau",
    ];
    $userPages=[
        "Achats"=>"#",
        "Demande de Subvention Sportive"=>"/subvention_inquiry",
        "Contact"=>"#",
    ];
    $adminPages=[
        "Adhérents"=>"/adherents",
        "Configuration shops annuelles"=>"#",
        "Configuration shops ponctuelles"=>"#",
        "Gestion des shops avec tickets dématérialisés"=>"#",
        "Gestion des shops avec tickets papier"=>"#",
        "Demandes de subvention sportive"=>"#",
        "Communiquer"=>"#"
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
                    <img src="{{ asset('images/icon.png') }}" alt="Adolices Logo" loading="lazy">
                    <div class="menu-icon">
                        <i class="fa-solid fa-window-minimize bar"></i>
                        <i class="fa-solid fa-window-minimize bar"></i>
                        <i class="fa-solid fa-window-minimize bar"></i>
                    </div>
                </button>
            </div>
            <a class="userSpace" href="{{ isset($current_user) ? route('profile') : route('login') }}">
                @if(isset($current_user))
                    @if (isset($userImage)&&file_exists($userImage))
                        <img id="userImage" src="{{asset($userImage)}}" alt="">
                    @else
                        <img id="userImage" src="{{asset("images/UserPhotos/DefaultUser.png")}}" alt="">
                    @endif
                    <p><strong>{{$current_user->first_name}}</strong></p>
                    <p>{{$current_user->last_name}}</p>
                @else
                    <i class="fa-solid fa-user-slash" id="disconected-user-image"></i>
                    <p>Se Connecter</p>
                @endif
            </a>
            <div id="title">
                <h1>Adolices</h1>
            </div>
        </section>
        <div id="menu-container">
            @php
                $currentTitle = trim(View::yieldContent('title') ?? '');
            @endphp
            @foreach ($disconectedPages as $page_title => $link )
                <a href={{ $link }} {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
            @endforeach
            @if($adhesion_valid)
                @foreach ($userPages as $page_title => $link )
                    <a href={{ $link }} {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
                @endforeach
                @if ($current_user->is_admin??false)
                    <div id="adminSeparator"></div>
                    <p id="adminSeparatorText">Pages d'administrations:</p>
                    @foreach ($adminPages as $page_title => $link )
                        <a href={{ $link }} class="admin_page" {{ $currentTitle == $page_title ? ' id=selectedPage' : '' }}>{{ $page_title }}</a>
                    @endforeach
                @endif
            @endif
        </div>
        <div id="menu-overlay"></div>

        <section id="content">
            @if (!(isset($admin_page)&&$admin_page))
                <img id=background_image src="{{asset('images/background_image.avif')}}" alt="">
            @endif

            @yield('content')
        </section>
        <section id="footer">

        </section>
    </body>

</html>
