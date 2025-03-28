<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Se Connecter</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="icon" href="{{ asset('images/icon.png') }}">
        <link rel="stylesheet" href="{{ asset('css/pages_css/login.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </head>

    <body>
        <section id="header" class="text-center mb-4">
            <div id="title">
                <h1>Adolices</h1>
                <h2>Se Connecter</h2>
            </div>
        </section>
        <img id=background_image src="{{asset('images/background_image.avif')}}" alt="">
        <section id="content">
            <div id="login-container">
                @if ($errors->any())
                    <div class="alert alert-dismissible alert-danger" id="error">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/login" class="form-floating mb-3">
                    @csrf

                    <div class="mb-3 form-floating">
                        <input type="email" name="email" id="email" placeholder="Email" class="form-control" required>
                        <label for="email">Email</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="password" placeholder="Mot de passe" name="password" id="password" class="form-control" autocomplete="current-password" required>
                        <label for="password" >Mot de passe</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Se Connecter</button>
                    </div>
                </form>
                <a href="{{route('register')}}">Créer un compte</a>
            </div>

        </section>

        <section id="footer">
        </section>
    </body>
</html>
