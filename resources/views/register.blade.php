@extends('template')

@section('title')
    Créer un compte
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/register.css') }}">
@endsection

@section('content')
    <h3 id="register-title">Créer un compte</h3>
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
                <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}" required placeholder="Last Name">
                <label for="last_name">Nom</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}" required placeholder="First Name">
                <label for="first_name">Prénom</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Email">
                <label for="email">Email</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="email" id="email_imt" name="email_imt" class="form-control" value="{{ old('email_imt') }}" placeholder="IMT Email">
                <label for="email_imt">Email IMT (Optionnel)</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="Phone Number">
                <label for="phone_number">Numéro de téléphone (Optionnel)</label>
            </div>

            <div class="mb-3 form-floating">
                <select id="status_id" name="status_id" class="form-select" required>
                    <option value="" selected disabled>Choisissez un statut</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->status_id }}">{{ $status->status_label }}</option>
                    @endforeach
                </select>
                <label for="status_id">Statut</label>
            </div>

            <div class="mb-3 form-floating">
                <select id="group_id" name="group_id" class="form-select" required>
                    <option value="" selected disabled>Choisissez un groupe</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->group_id }}">{{ $group->label_group }}</option>
                    @endforeach
                </select>
                <label for="group_id">Service</label>
            </div>

            <div class="mb-3">
                <label for="site_ids" class="form-label">Vos Sites de Référence</label>
                <select id="site_ids" name="site_ids[]" class="form-select" multiple>
                    @foreach ($sites as $site)
                        <option value="{{ $site->site_id }}" {{ in_array($site->site_id, old('site_ids', [])) ? 'selected' : '' }}>
                            {{ $site->label_site }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs sites.</small>
            </div>

            <div class="mb-3 form-floating">
                <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required placeholder="Password">
                <label for="password">Mot de passe</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="password" id="password-confirm" name="password_confirmation" autocomplete="current-password" class="form-control" required placeholder="Confirm Password">
                <label for="password-confirm">Confirmation du mot de passe</label>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100">Créer un compte</button>
            </div>

            <div class="mb-3">
                <p class="text-center">Déjà un compte ? <a href="{{ route('login') }}">Connectez-vous ici</a></p>
            </div>
        </form>
    </div>
@endsection
