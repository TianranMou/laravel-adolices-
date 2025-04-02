@extends('template')

@section('title')
    Mon Profil
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/profile.css') }}">
    <script src="{{ asset('js/pages_js/profile.js') }}" defer></script>
@endsection

@section('content')
    <h1 id="profile-title">Mon profil</h1>
    <div id="profile-content">
        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <!-- Informations personnelles -->
            <div class="mb-3 form-floating">
                <input type="text" id="last_name" name="last_name" class="form-control" value="{{ $current_user->last_name }}" required>
                <label for="last_name">Nom</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="first_name" name="first_name" class="form-control" value="{{ $current_user->first_name }}" required>
                <label for="first_name">Prénom</label>
            </div>

            <!-- Status -->
            <div class="mb-3 form-floating">
                <select id="status_id" name="status_id" class="form-select" required>
                    <option value="" disabled>Choisissez un statut</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->status_id }}" {{ $current_user->status_id == $status->status_id ? 'selected' : '' }}>
                            {{ $status->status_label }}
                        </option>
                    @endforeach
                </select>
                <label for="status_id">Statut</label>
            </div>

            <!-- Group -->
            <div class="mb-3 form-floating">
                <select id="group_id" name="group_id" class="form-select" required>
                    <option value="" disabled>Choisissez un groupe</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->group_id }}" {{ $current_user->group_id == $group->group_id ? 'selected' : '' }}>
                            {{ $group->label_group }}
                        </option>
                    @endforeach
                </select>
                <label for="group_id">Groupe</label>
            </div>

            <!-- Informations de contact -->
            <div class="mb-3 form-floating">
                <input type="email" id="email" name="email" class="form-control" value="{{ $current_user->email }}" required>
                <label for="email">Email</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="email" id="email_imt" name="email_imt" class="form-control" value="{{ $current_user->email_imt ?? '' }}">
                <label for="email_imt">IMT Email</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ $current_user->phone_number ?? '' }}">
                <label for="phone_number">Numéro de téléphone</label>
            </div>

            <!-- Photo de profil -->
            <div class="mb-3">
                <label for="photo" class="form-label">Photo de profil</label>
                <div class="profile-photo-container mb-2">
                    <img src="{{ $current_user->photo ? asset($current_user->photo) : asset('images/default-profile.png') }}" alt="Photo de profil" id="profile_photo_preview" class="img-thumbnail">
                </div>
                <input type="file" id="photo" name="photo" class="form-control">
            </div>

            <!-- Sites de référence -->
            <div class="mb-3">
                <label for="site_ids" class="form-label">Sites de Référence</label>
                <select id="site_ids" name="site_ids[]" class="form-select" multiple>
                    @foreach ($sites as $site)
                        <option value="{{ $site->site_id }}"
                            {{ in_array($site->site_id, $current_user->sites->pluck('site_id')->toArray()) ? 'selected' : '' }}>
                            {{ $site->label_site }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs sites.</small>
            </div>

            <!-- Autorisations -->
            <div class="mb-3">
                <label for="photo_release" class="form-label">Autorisation de diffusion des photos</label>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="photo_release" name="photo_release" value="1" {{ $current_user->photo_release ? 'checked' : '' }}>
                    <label class="form-check-label" for="photo_release">Autoriser la diffusion de mes photos</label>
                </div>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="photo_consent" name="photo_consent" value="1" {{ $current_user->photo_consent ? 'checked' : '' }}>
                    <label class="form-check-label" for="photo_consent">Consentement à la prise de photos</label>
                </div>
            </div>

            <!-- Boutons -->
            <div class="mb-3">
                <button type="submit" id="save-button" class="btn btn-success">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection
