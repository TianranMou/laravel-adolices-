@extends('template')

@section('title')
    Créer un compte
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/register.css') }}">
    <script src="{{ asset('js/pages_js/register.js') }}"></script>
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

            <div class="mb-3">
                <label for="email_type" class="form-label">Type d'email</label>
                <select id="email_type" name="email_type" class="form-select" onchange="toggleEmailFields()">
                    <option value="email" {{ old('email_type') == 'email' ? 'selected' : '' }}>Email personnel</option>
                    <option value="email_imt" {{ old('email_type') == 'email_imt' ? 'selected' : '' }}>Email IMT</option>
                </select>
            </div>

            <div class="mb-3 form-floating" id="email_imt_container">
                <input type="email" id="email_imt" name="email_imt" class="form-control" value="{{ old('email_imt') }}" placeholder="IMT Email">
                <label for="email_imt">Email IMT</label>
            </div>

            <div id="email_also_container" style="display: none;" class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="email_also_checkbox" onchange="toggleEmailAlso()">
                    <label class="form-check-label" for="email_also_checkbox">
                        Ajouter un email personnel
                    </label>
                </div>
            </div>

            <div class="mb-3 form-floating">
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email">
                <label for="email">Email</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="Phone Number">
                <label for="phone_number">Numéro de téléphone (Optionnel)</label>
            </div>

            <div class="mb-3 form-floating">
                <select id="status_id" name="status_id" class="form-select" required>
                    <option value="" selected disabled>Choisissez un statut</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->status_id }}" {{ old('status_id') == $status->status_id ? 'selected' : '' }}>{{ $status->status_label }}</option>
                    @endforeach
                </select>
                <label for="status_id">Statut</label>
            </div>

            <div class="mb-3 form-floating">
                <select id="group_id" name="group_id" class="form-select" required>
                    <option value="" selected disabled>Choisissez un groupe</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->group_id }}" {{ old('group_id') == $group->group_id ? 'selected' : '' }}>{{ $group->label_group }}</option>
                    @endforeach
                </select>
                <label for="group_id">Service</label>
            </div>

            <div class="mb-3">
                <label for="site_ids" class="form-label">Vos Sites de Référence</label>
                <div class="site-checkboxes">
                    @foreach ($sites as $site)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="site_ids[]" id="site_{{ $site->site_id }}" value="{{ $site->site_id }}" {{ in_array($site->site_id, old('site_ids', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="site_{{ $site->site_id }}">
                                {{ $site->label_site }}
                            </label>
                        </div>
                    @endforeach
                </div>
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
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="photo_release" id="photo_release" value="1" {{ old('photo_release') ? 'checked' : '' }}>
                    <label class="form-check-label" for="photo_release">
                        J'accepte que mon image soit utilisée pour la communication externe de l'association.
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="photo_consent" id="photo_consent" value="1" {{ old('photo_consent') ? 'checked' : '' }}>
                    <label class="form-check-label" for="photo_consent">
                        J'accepte de figurer sur les photos prises dans le cadre des activités de l'association.
                    </label>
                </div>
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
