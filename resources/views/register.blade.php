<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Créer Un Compte</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="icon" href="{{ asset('images/icon.png') }}">

        <link rel="stylesheet" href="{{ asset('css/pages_css/register.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.css')}}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>

    <body>
        <section id="header" class="text-center mb-4">
            <div id="title">
                <h1>Adolices</h1>
                <h2>Créer Un Compte</h2>
            </div>
        </section>
        <img id=background_image src="{{asset('images/background_image.avif')}}" alt="">
        <section id="content">

            <div id="register-container">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3 form-floating">
                        <select id="status_id" name="status_id" class="form-select" required>
                            <option value="" selected disabled>Choisissez un statut</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->status_id }}">{{ $status->status_label }}</option>
                            @endforeach
                        </select>
                        <label for="status_id">Status</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <select id="group_id" name="group_id" class="form-select" required>
                            <option value="" selected disabled>Choisissez un groupe</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->group_id }}">{{ $group->label_group }}</option>
                            @endforeach
                        </select>
                        <label for="group_id">Group</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" required placeholder="Last Name">
                        <label for="last_name">Last Name</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" required placeholder="First Name">
                        <label for="first_name">First Name</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Email">
                        <label for="email">Email</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="email" id="email_imt" name="email_imt" class="form-control" value="{{ old('email_imt') }}" placeholder="IMT Email">
                        <label for="email_imt">IMT Email</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required placeholder="Password">
                        <label for="password">Password</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="password" id="password-confirm" name="password_confirmation" autocomplete="current-password" class="form-control" required placeholder="Confirm Password">
                        <label for="password-confirm">Confirm Password</label>
                    </div>

                    <div class="mb-3 form-floating">
                        <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="Phone Number">
                        <label for="phone_number">Phone Number</label>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100">Créer un Compte</button>
                    </div>
                </form>
                <a href="{{route('login')}}">Se Connecter</a>
            </div>

        </section>

        <section id="footer">
        </section>

    </body>
</html>
