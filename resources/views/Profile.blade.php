@extends('template')

@section('title')
    Mon Profil
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/profile.css') }}">
    <script src="{{asset('js/pages_js/profile.js')}}" defer></script>
@endsection

@section('content')
    <h1 id="profile-title">Mon Profil</h1>
    <div id="profile-content">
        <form id="profile-form" method="POST">
            @csrf
            <div class="mb-3 form-floating">
                <select id="status_id" name="status_id" class="form-select" required disabled>
                    <option value="" disabled>Choisissez un statut</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->status_id }}" {{ $current_user->status_id == $status->status_id ? 'selected' : '' }}>{{ $status->status_label }}</option>
                    @endforeach
                </select>
                <label for="status_id">Statut</label>
            </div>

            <div class="mb-3 form-floating">
                <select id="group_id" name="group_id" class="form-select" required disabled>
                    <option value="" disabled>Choisissez un groupe</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->group_id }}" {{ $current_user->group_id == $group->group_id ? 'selected' : '' }}>{{ $group->label_group }}</option>
                    @endforeach
                </select>
                <label for="group_id">Groupe</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="last_name" name="last_name" class="form-control" value="{{ $current_user->last_name }}" required disabled>
                <label for="last_name">Nom</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="first_name" name="first_name" class="form-control" value="{{ $current_user->first_name }}" required disabled>
                <label for="first_name">Prénom</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="email" id="email" name="email" class="form-control" value="{{ $current_user->email }}" required disabled>
                <label for="email">Email</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="email" id="email_imt" name="email_imt" class="form-control" value="{{ $current_user->email_imt ?? 'Non renseigné' }}" disabled>
                <label for="email_imt">IMT Email</label>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ $current_user->phone_number ?? 'Non renseigné' }}" disabled>
                <label for="phone_number">Numéro de téléphone</label>
            </div>

            <!-- Droit de diffusion -->
            <div class="mb-3">
                <label for="photo_release" class="form-label">Autorisation de diffusion des photos</label>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="photo_release" name="photo_release" @if($current_user->photo_release) checked @endif disabled>
                    <label class="form-check-label" for="photo_release">Autoriser la diffusion de mes photos</label>
                </div>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="photo_consent" name="photo_consent" @if($current_user->photo_consent) checked @endif disabled>
                    <label class="form-check-label" for="photo_consent">Consentement à la prise de photos</label>
                </div>
            </div>

            <div class="mb-3 form-floating">
                <input type="text" id="profile_photo" name="profile_photo" class="form-control" value="{{ $current_user->profile_photo_url ?? 'Aucune photo' }}" disabled>
                <label for="profile_photo">Photo de Profil</label>
            </div>

            <div class="mb-3">
                <button type="button" id="edit-button" class="btn btn-primary">Modifier</button>
            </div>
        </form>
        <div class="mb-3">
            <form action="{{ route('logout') }}" method="GET" id="logout-form">
                @csrf
                <button type="submit" class="btn btn-danger">Se Déconnecter</button>
            </form>
        </div>
    </div>
@endsection
