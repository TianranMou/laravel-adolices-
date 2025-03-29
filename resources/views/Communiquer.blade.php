@extends('template')

@php
    $admin_page=true;
@endphp

@section('title')
    Créer une communication
@endsection

@section('head')
    <link rel="stylesheet" href="{{asset('css/pages_css/communiquer.css')}}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.0/tinymce.min.js"></script>
    <script src="{{ asset('js/pages_js/communiquer.js') }}"></script>
@endsection

@section('content')
    <div id="communication-content">
        <h1>Créer une communication</h1>
        <form id="communication-form" method="POST" action="{{ route('communiquer') }}">
            @csrf
            <div class="mb-3">
                <label for="subject" class="form-label">Titre de l'actualité</label>
                <input type="text" class="form-control" id="subject" name="subject"
                    value="{{ $oldValues['subject'] ?? '' }}" required>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Contenu de l'actualité</label>
                <textarea class="form-control" id="contentf" name="content" rows="5">{{ $oldValues['content'] ?? '' }}</textarea>
                <div class="invalid-feedback">
                    Veuillez saisir le contenu de l'actualité
                </div>
            </div>

            <!-- Sélecteur de templates d'email -->
            <div class="mb-3">
                <label for="template_select" class="form-label">Choisir un template de mail</label>
                <select id="template_select" class="form-control" onchange="loadTemplateIntoEditor(this)">
                    <option value="">-- Sélectionner un template --</option>
                    @foreach($templates as $template)
                        <option value="{{ $template['content'] }}">{{ $template['subject'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3" id="email-container">
                <label for="email-address">Adresses email</label>
                @if(isset($oldValues['email_addresses']) && count($oldValues['email_addresses']) > 0)
                    @foreach($oldValues['email_addresses'] as $email)
                        <div class="d-flex mb-2">
                            <input type="email" name="email_addresses[]" class="form-control me-2"
                                placeholder="Adresse email" value="{{ $email }}" required>
                            @if(!$loop->first)
                                <button type="button" class="btn btn-danger"
                                    onclick="this.parentNode.remove();">&times;</button>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="d-flex mb-2">
                        <input type="email" name="email_addresses[]" class="form-control me-2"
                            placeholder="Adresse email" required>
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-secondary mb-3" onclick="addEmailField()">Ajouter une adresse</button>

            <!-- Rocket Chat Integration -->
            <div class="mb-3">
                <label class="form-label">Envoyer sur Rocket Chat</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="rocket_chat_type" id="rocket_chat_channel" value="channel" {{ isset($oldValues['rocket_chat_type']) && $oldValues['rocket_chat_type'] == 'channel' ? 'checked' : '' }}>
                    <label class="form-check-label" for="rocket_chat_channel">
                        Envoyer à un canal
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="rocket_chat_type" id="rocket_chat_user" value="user" {{ isset($oldValues['rocket_chat_type']) && $oldValues['rocket_chat_type'] == 'user' ? 'checked' : '' }}>
                    <label class="form-check-label" for="rocket_chat_user">
                        Envoyer à un utilisateur
                    </label>
                </div>
            </div>

            <!-- Channel Selection (shown when channel is selected) -->
            <div class="mb-3 rocket-chat-target" id="channel-selection">
                <label for="rocket_chat_channel_name" class="form-label">Nom du canal</label>
                <input type="text" class="form-control" id="rocket_chat_channel_name" name="rocket_chat_channel_name"
                       value="{{ $oldValues['rocket_chat_channel_name'] ?? '' }}" placeholder="general">
            </div>

            <!-- User Selection (shown when user is selected) -->
            <div class="mb-3 rocket-chat-target" id="user-selection">
                <label for="rocket_chat_username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="rocket_chat_username" name="rocket_chat_username"
                       value="{{ $oldValues['rocket_chat_username'] ?? '' }}" placeholder="@username">
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Prévisualiser</button>
            </div>
        </form>
    </div>
@endsection
