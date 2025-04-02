@extends('template')

@php
    $admin_page=true;
@endphp

@section('title')
    Communication
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('css/pages_css/communiquer.css')}}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.0/tinymce.min.js"></script>
    <script src="{{ asset('js/pages_js/communiquer.js') }}"></script>
@endsection

@section('content')
    <div id="communication-content">
        <h1>Créer une communication</h1>
        <form id="communication-form" method="POST" action="{{ route('communiquer') }}"
            data-rocketchat-url="{{ route('rocketchat.send') }}">
            @csrf

            <!-- Communication Type Selection -->
            <div class="mb-4">
                <label class="form-label fw-bold">Type de communication</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="communication_type" id="communication_type_email" value="email" {{ !isset($oldValues['communication_type']) || $oldValues['communication_type'] == 'email' ? 'checked' : '' }}>
                    <label class="form-check-label" for="communication_type_email">
                        Envoyer par email
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="communication_type" id="communication_type_rocket" value="rocket" {{ isset($oldValues['communication_type']) && $oldValues['communication_type'] == 'rocket' ? 'checked' : '' }}>
                    <label class="form-check-label" for="communication_type_rocket">
                        Envoyer via Rocket Chat
                    </label>
                </div>
            </div>

            <!-- Template Selector - Only for Email -->
            <div id="template-section">
                <!-- Sélecteur de templates d'email -->
                <div class="mb-3" id="template-selector">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="template_select" class="form-label mb-0">Choisir un template de mail</label>
                        <a href="{{ route('template-mail.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-cog"></i> Gérer les templates
                        </a>
                    </div>
                    <select id="template_select" class="form-control" onchange="loadTemplateIntoEditor(this)">
                        <option value="">-- Sélectionner un template --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->content }}">{{ $template->subject }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Subject field - Only shown for Email -->
            <div class="mb-3" id="subject-section">
                <label for="subject" class="form-label">Sujet de la communication</label>
                <input type="text" class="form-control" id="subject" name="subject"
                    value="{{ $oldValues['subject'] ?? '' }}" required>
            </div>

            <!-- Email content with TinyMCE -->
            <div id="email-content-section" class="mb-3">
                <label for="contentf" class="form-label">Contenu de la communication</label>
                <textarea class="form-control" id="contentf" name="content" rows="5">{{ $oldValues['content'] ?? '' }}</textarea>
                <div class="invalid-feedback">
                    Veuillez saisir le contenu de la communication
                </div>
            </div>

            <!-- Template Save Button - After content -->
            <div class="d-flex justify-content-end mb-3" id="save-template-container">
                <button type="button" id="save-template-btn" class="btn btn-outline-primary">
                    <i class="fas fa-save"></i> Sauvegarder comme template
                </button>
            </div>

            <!-- Rocket Chat content (plain text) -->
            <div id="rocket-content-section" class="mb-3" style="display: none;">
                <label for="rocket_content" class="form-label">Message Rocket Chat</label>
                <textarea class="form-control" id="rocket_content" name="rocket_content" rows="5">{{ $oldValues['rocket_content'] ?? $oldValues['content'] ?? '' }}</textarea>
                <div class="invalid-feedback">
                    Veuillez saisir votre message
                </div>
            </div>

            <!-- Email Section -->
            <div id="email-section">
                <h4 class="mt-4 mb-3">Configuration Email</h4>
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
            </div>

            <!-- Rocket Chat Section -->
            <div id="rocket-chat-section">
                <h4 class="mt-4 mb-3">Configuration Rocket Chat</h4>
                <!-- Rocket Chat Type Selection -->
                <div class="mb-3">
                    <label class="form-label">Type de destinataire</label>
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

                <!-- Channel Selection -->
                <div class="mb-3 rocket-chat-target" id="channel-selection">
                    <label for="rocket_chat_channel_name" class="form-label">Nom du canal</label>
                    <input type="text" class="form-control" id="rocket_chat_channel_name" name="rocket_chat_channel_name"
                           value="{{ $oldValues['rocket_chat_channel_name'] ?? '' }}" placeholder="general">
                </div>

                <!-- User Selection -->
                <div class="mb-3 rocket-chat-target" id="user-selection">
                    <label for="rocket_chat_username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="rocket_chat_username" name="rocket_chat_username"
                           value="{{ $oldValues['rocket_chat_username'] ?? '' }}" placeholder="@username">
                </div>
            </div>
            <div class="mb-3">
                <button type="submit" id="submit-button" class="btn btn-primary">
                    {{ !isset($oldValues['communication_type']) || $oldValues['communication_type'] == 'email' ? 'Prévisualiser' : 'Envoyer' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Modal de confirmation pour la sauvegarde du template -->
    <div class="modal fade" id="templateSaveModal" tabindex="-1" aria-labelledby="templateSaveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="templateSaveModalLabel">Sauvegarde du template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="templateSaveModalBody">
                    <!-- Le message sera inséré ici -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
@endsection
