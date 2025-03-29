<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="{{ asset('images/icon.png') }}">
    <link rel="stylesheet" href="{{ asset("css/style.css")}}">
    <link rel="stylesheet" href="{{ asset("css/bootstrap.css")}}">
</head>
<body>
    <section id="header" class="text-center mb-4">
        <div id="title">
            <h1>Adolices</h1>
            <h2>Réinitialiser le mot de passe</h2>
        </div>
    </section>
    <img id=background_image src="{{asset('images/background_image.avif')}}" alt="">
    <section id="content">
        <div id="login-container">
            <form method="POST" action="{{ route('password.update') }}" class="form-floating mb-3">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3 form-floating">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Email">
                    <label for="email">Email</label>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3 form-floating">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Nouveau mot de passe">
                    <label for="password">Nouveau mot de passe</label>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3 form-floating">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmer le nouveau mot de passe">
                    <label for="password-confirm">Confirmer le nouveau mot de passe</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Réinitialiser le mot de passe</button>
                </div>
            </form>
        </div>
    </section>
    <section id="footer"></section>
</body>
</html>
