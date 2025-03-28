@extends('template')

@section('title')
    Adhérents
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/buttons.bootstrap5.min.css') }}">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-4">
                            <div class="flex-grow-1">
                                <select id="schoolYear" class="form-select">
                                    <option value="all">Tout</option>
                                    @for ($year = env('FIRST_SCHOOL_YEAR'); $year <= date('Y'); $year++)
                                        <option value="{{ $year }}">{{ $year . '-' . ($year + 1) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showAdherentsOnly" style="width: 3em; height: 1.5em;">
                                <label class="form-check-label ms-2 text-muted" for="showAdherentsOnly">
                                    <i class="fa fa-users me-2"></i>Afficher uniquement les adhérents
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end d-flex align-items-center justify-content-end">
                <button class="btn btn-primary" id="addAdherent">
                    <i class="fa fa-plus me-2"></i> Ajouter un adhérent
                </button>
            </div>
        </div>

        <table id="adherentsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Service</th>
                    <th>Site</th>
                    <th>Nombre d'enfants</th>
                    <th>Conjoint</th>
                    <th>Adhesion</th>
                    <th>Date d'adhésion</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr data-has-active-adhesion="{{ isset($user['adhesions'][0]) ? 'true' : 'false' }}">
                    <td>{{ $user['last_name'] }}</td>
                    <td>{{ $user['first_name'] }}</td>
                    <td>{{ $user['group']['label_group'] ?? 'N/A' }}</td>
                    <td>{{ $user['sites'][0]['label_site'] ?? 'N/A' }}</td>
                    <td>{{ $user['family_members']['child_nb'] ?? '0' }}</td>
                    <td>{{ $user['family_members']['spouse'] === 'true' ? 'Oui' : 'Non' }}</td>
                    <td>{{ isset($user['adhesions'][0]) ? 'Oui' : 'Non' }}</td>
                    <td>{{ isset($user['adhesions'][0]) ? \Carbon\Carbon::parse($user['adhesions'][0]['date_adhesion'])->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-adherent" data-id="{{ $user['user_id'] }}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-adherent" data-id="{{ $user['user_id'] }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Adherent Modal -->
    <div class="modal fade" id="adherentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adhérent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="adherentForm">
                        <input type="hidden" id="userId">
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="firstName" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail personnel</label>
                            <input type="text" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="email_imt" class="form-label">E-mail IMT</label>
                            <input type="text" class="form-control" id="email_imt">
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Numéro de téléphone</label>
                            <input type="text" class="form-control" id="phone_number">
                        </div>
                        <div class="mb-3">
                            <label for="service" class="form-label">Service</label>
                            <select class="form-select" id="service" required>
                                @foreach($groups as $group)
                                    <option value="{{ $group->group_id }}">{{ $group->label_group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="site" class="form-label">Site</label>
                            <select class="form-select" id="site" required>
                                @foreach($sites as $site)
                                    <option value="{{ $site->site_id }}">{{ $site->label_site }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" required>
                                @foreach($status as $status)
                                    <option value="{{ $status->status_id }}">{{ $status->status_label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                <input type="checkbox" role="switch" class="form-check-input" id="adhesion" style="width: 3em; height: 1.5em;">
                                <label class="form-check-label" for="adhesion">Adhésion</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                <input type="checkbox" role="switch" class="form-check-input" id="photo_release" style="width: 3em; height: 1.5em;">
                                <label class="form-check-label" for="photo_release">Publication des photos</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                <input type="checkbox" role="switch" class="form-check-input" id="photo_consent" style="width: 3em; height: 1.5em;">
                                <label class="form-check-label" for="photo_consent">Prise en photo</label>
                            </div>
                        </div>
                        <hr>
                        <h5>Membres de la famille</h5>
                        <div id="familyMembersContainer">
                            <!-- Family members will be added here dynamically -->
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm mb-3" id="addFamilyMember">
                            <i class="fa fa-plus"></i> Ajouter un membre
                        </button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="saveAdherent">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
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
    <script>
        window.initialUsers = @json($users);
    </script>
    <script src="{{ asset('js/pages_js/Adherents.js') }}"></script>
@endsection
