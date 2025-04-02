@extends('template')

@section('title')
    Templates d'email
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap5.min.css') }}">
@endsection

@section('content')
    <div class="container mt-4">
        <!-- Bouton retour -->
        <div class="mb-3">
            <a href="{{ route('communiquer') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-2"></i> Retour à la page communication
            </a>
        </div>
        <!-- Titre et bouton d'ajout -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="fs-4">Gestion des templates d'email</h2>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" id="addTemplate">
                    <i class="fa fa-plus me-2"></i> Ajouter un template
                </button>
            </div>
        </div>

        <table id="templateMailTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Objet</th>
                    <th>Boutique associée</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td>{{ $template->subject }}</td>
                    <td>{{ $template->shop ? $template->shop->shop_name : 'Aucune' }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary edit-template" data-id="{{ $template->mail_template_id }}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-template" data-id="{{ $template->mail_template_id }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal pour éditer/ajouter un template -->
    <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="templateModalLabel">Template Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="templateForm">
                        <input type="hidden" id="templateId" name="templateId">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Objet</label>
                            <input type="text" class="form-control" id="subject" name="subject" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenu</label>
                            <textarea class="form-control" id="mail_content" name="mail_content" rows="10" required></textarea>
                            <small class="form-text text-muted">Vous pouvez insérer du contenu HTML pour personnaliser l'email.</small>
                        </div>
                        <div class="mb-3">
                            <label for="shop_id" class="form-label">Boutique associée</label>
                            <select class="form-select" id="shop_id" name="shop_id">
                                <option value="">Aucune</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->shop_id }}">{{ $shop->shop_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="saveTemplate">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fa fa-info-circle me-2"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <!-- Message will be inserted here -->
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/pages_js/template_mail.js') }}"></script>
@endsection
