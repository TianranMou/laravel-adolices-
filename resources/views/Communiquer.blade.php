@extends('template')

@php
    // Liste des templates avec leur contenu HTML
    $templates = [
        [
            'id' => 'template_1',
            'name' => 'Template de bienvenue',
            'content' => '<h1>Bienvenue sur notre service</h1><p>Ceci est un email de bienvenue pour notre service.</p>',
        ],
        [
            'id' => 'template_2',
            'name' => 'Template d\'actualité',
            'content' => '<h1>Dernières nouvelles</h1><p>Ceci est un template d\'actualité avec des informations importantes.</p>',
        ],
        // Ajouter d'autres templates ici
    ];
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
        <form id="communication-form" method="POST" action="">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Titre de l'actualité</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Contenu de l'actualité</label>
                <textarea class="form-control" id="contentf" name="content" rows="5" required></textarea>
            </div>

            <!-- Sélecteur de templates d'email -->
            <div class="mb-3">
                <label for="template_select" class="form-label">Choisir un template</label>
                <select id="template_select" class="form-control" onchange="loadTemplateIntoEditor(this)">
                    <option value="">-- Sélectionner un template --</option>
                    @foreach($templates as $template)
                        <option value="{{ $template['content'] }}">{{ $template['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-check-label" for="email_send">Envoyer par Email</label>
                <input type="checkbox" class="form-check-input" id="email_send" name="email_send">
            </div>

            <div class="mb-3" id="email-container">
                <label for="email-address">Adresses Email</label>
                <input type="email" name="email_addresses[]" class="form-control mb-2" placeholder="Adresse Email">
            </div>
            <button type="button" class="btn btn-secondary mb-3" onclick="addEmailField()">Ajouter une adresse</button>

            <div class="mb-3">
                <label for="wordpress_send" class="form-label">Envoyer sur MyServices</label>
                <input type="checkbox" class="form-check-input" id="wordpress_send" name="wordpress_send">
            </div>

            <div class="mb-3">
                <label for="rocketchat_send" class="form-label">Envoyer sur RocketChat</label>
                <input type="checkbox" class="form-check-input" id="rocketchat_send" name="rocketchat_send">
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Envoyer la communication</button>
            </div>
        </form>
    </div>
@endsection
