@extends('template')

@section('title')
    Adhérents
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <select id="schoolYear" class="form-select me-3">
                        <option value="all">Tout</option>
                        <option value="2023" {{ isset($year) && $year == '2023' ? 'selected' : '' }}>2023-2024</option>
                        <option value="2024" {{ isset($year) && $year == '2024' ? 'selected' : '' }}>2024-2025</option>
                        <option value="2025" {{ isset($year) && $year == '2025' ? 'selected' : '' }}>2025-2026</option>
                    </select>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="showAdherentsOnly">
                        <label class="form-check-label" for="showAdherentsOnly">
                            Afficher uniquement les adhérents
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" id="addAdherent">
                    <i class="fa fa-plus"></i> Ajouter un adhérent
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
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="adhesion">
                                <label class="form-check-label" for="adhesion">Adhésion</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="photo_release">
                                <label class="form-check-label" for="photo_release">Publication des photos</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="photo_consent">
                                <label class="form-check-label" for="photo_consent">Prise en photo</label>
                            </div>
                        </div>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add this at the beginning of your script
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            const table = $('#adherentsTable').DataTable({
                language: {
                    "emptyTable": "Aucune donnée disponible dans le tableau",
                    "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    "infoEmpty": "Affichage de 0 à 0 sur 0 entrée",
                    "infoFiltered": "(filtré de _MAX_ entrées au total)",
                    "infoThousands": ",",
                    "lengthMenu": "Afficher _MENU_ entrées",
                    "loadingRecords": "Chargement...",
                    "processing": "Traitement...",
                    "search": "Rechercher :",
                    "zeroRecords": "Aucun élément correspondant trouvé",
                    "paginate": {
                        "first": "Premier",
                        "last": "Dernier",
                        "next": "Suivant",
                        "previous": "Précédent"
                    },
                    "aria": {
                        "sortAscending": ": activer pour trier la colonne par ordre croissant",
                        "sortDescending": ": activer pour trier la colonne par ordre décroissant"
                    },
                    "select": {
                        "rows": {
                            "_": "%d lignes sélectionnées",
                            "1": "1 ligne sélectionnée"
                        }
                    }
                },
                order: [[0, 'asc']],
                pageLength: 25,
                createdRow: function(row, data, dataIndex) {
                    // Get the adhesion status from column 6 (Adhesion column)
                    const hasAdhesion = data[6] === 'Oui';
                    $(row).attr('data-has-active-adhesion', hasAdhesion ? 'true' : 'false');
                }
            });

            // Filter adherents checkbox handler
            $('#showAdherentsOnly').change(function() {
                const showOnlyAdherents = $(this).is(':checked');

                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    if (!showOnlyAdherents) return true;
                    return data[6] === 'Oui';
                });

                table.draw();

                $.fn.dataTable.ext.search.pop();
            });

            // School year change handler
            $('#schoolYear').change(function() {
                const year = $(this).val().split('-')[0];

                // Show loading indicator
                table.clear().draw();
                $('tbody').append('<tr><td colspan="8" class="text-center">Chargement des données...</td></tr>');

                // If "all" is selected, use the initial data
                if (year === 'all') {
                    const initialData = @json($users);
                    table.clear();

                    if (initialData && initialData.length > 0) {
                        initialData.forEach(function(user) {
                            let adhesionDate = 'N/A';
                            if (user.adhesions && user.adhesions.length > 0) {
                                const date = new Date(user.adhesions[0].date_adhesion);
                                adhesionDate = date.toLocaleDateString('fr-FR');
                            }

                            table.row.add([
                                user.last_name,
                                user.first_name,
                                user.group?.label_group || 'N/A',
                                user.sites?.[0]?.label_site || 'N/A',
                                user.family_members?.child_nb || '0',
                                user.family_members?.spouse === 'true' ? 'Oui' : 'Non',
                                user.adhesions?.[0] ? 'Oui' : 'Non',
                                adhesionDate,
                                `<button class="btn btn-sm btn-primary edit-adherent" data-id="${user.user_id}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-adherent" data-id="${user.user_id}">
                                    <i class="fa fa-trash"></i>
                                </button>`
                            ]).draw();
                        });
                    } else {
                        table.clear().draw();
                        $('tbody').append('<tr><td colspan="8" class="text-center">Aucun utilisateur trouvé</td></tr>');
                    }

                    // Update URL without page reload
                    const url = new URL(window.location);
                    url.searchParams.delete('year');
                    window.history.pushState({}, '', url);

                    // Reattach event handlers
                    attachEventHandlers();
                    return;
                }

                // For specific years, use the API endpoint
                $.ajax({
                    url: `/adherents/year/${year}`,
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        // Clear the table
                        table.clear();

                        // Process and add each user to the table
                        if (response.adherents && response.adherents.length > 0) {
                            response.adherents.forEach(function(user) {
                                let adhesionDate = 'N/A';
                                if (user.adhesions && user.adhesions.length > 0) {
                                    const date = new Date(user.adhesions[0].date_adhesion);
                                    adhesionDate = date.toLocaleDateString('fr-FR');
                                }

                                // Format data for display
                                let childrenCount = user.family_members?.child_nb || '0';
                                let hasSpouse = user.family_members?.spouse === 'true' ? 'Oui' : 'Non';
                                let site = user.sites?.[0]?.label_site || 'N/A';
                                let groupLabel = user.group?.label_group || 'N/A';

                                table.row.add([
                                    user.last_name,
                                    user.first_name,
                                    groupLabel,
                                    site,
                                    childrenCount,
                                    hasSpouse,
                                    user.adhesions?.[0] ? 'Oui' : 'Non',
                                    adhesionDate,
                                    `<button class="btn btn-sm btn-primary edit-adherent" data-id="${user.user_id}">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-adherent" data-id="${user.user_id}">
                                        <i class="fa fa-trash"></i>
                                    </button>`
                                ]).draw();
                            });
                        } else {
                            // No users found
                            table.clear().draw();
                            $('tbody').append('<tr><td colspan="8" class="text-center">Aucun utilisateur trouvé pour cette année</td></tr>');
                        }

                        // Update URL without page reload
                        const url = new URL(window.location);
                        url.searchParams.set('year', year);
                        window.history.pushState({}, '', url);

                        // Reattach event handlers to the newly created buttons
                        attachEventHandlers();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching users:', error);
                        console.log('Response text:', xhr.responseText);
                        table.clear().draw();
                        $('tbody').append('<tr><td colspan="8" class="text-center">Erreur lors du chargement des données</td></tr>');
                    }
                });
            });

            function attachEventHandlers() {
                // Edit adherent button handler
                $('.edit-adherent').click(function() {
                    const userId = $(this).data('id');

                    // Fetch adherent data
                    $.ajax({
                        url: `/adherents/${userId}`,
                        method: 'GET',
                        success: function(response) {
                            console.log('Received adherent data:', response);

                            // Populate the form with adherent data
                            $('#userId').val(response.user_id);
                            $('#lastName').val(response.last_name);
                            $('#firstName').val(response.first_name);
                            $('#email').val(response.email);
                            $('#email_imt').val(response.email_imt);
                            $('#phone_number').val(response.phone_number);
                            $('#service').val(response.group_id);

                            // Handle site selection
                            if (response.site_id) {
                                $('#site').val(response.site_id);
                                console.log('Setting site_id:', response.site_id);
                            } else {
                                console.log('No site_id found in response');
                            }

                            $('#status').val(response.status_id);
                            $('#adhesion').prop('checked', response.adhesion_valid);
                            $('#photo_release').prop('checked', response.photo_release);
                            $('#photo_consent').prop('checked', response.photo_consent);

                            // Show the modal
                            $('#adherentModal').modal('show');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching adherent data:', error);
                            showToast('Une erreur est survenue lors du chargement des données de l\'adhérent', 'error');
                        }
                    });
                });

                // Function for confirmation
                function showConfirmation(message, callback) {
                    $('#confirmationMessage').text(message);
                    $('#confirmationModal').modal('show');

                    // Remove any existing click handlers
                    $('#confirmDelete').off('click');

                    // Add new click handler
                    $('#confirmDelete').on('click', function() {
                        $('#confirmationModal').modal('hide');
                        callback();
                    });
                }

                // Delete handler
                $('.delete-adherent').click(function() {
                    const userId = $(this).data('id');
                    const adherentName = $(this).closest('tr').find('td:first').text() + ' ' + $(this).closest('tr').find('td:nth-child(2)').text();

                    showConfirmation(`Êtes-vous sûr de vouloir supprimer l'adhérent ${adherentName} ?`, function() {
                        $.ajax({
                            url: `/adherents/${userId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                console.log('Success:', response);
                                // Remove the row from the DataTable
                                const row = $(this).closest('tr');
                                table.row(row).remove().draw();

                                // Show success message using toast
                                showToast('Adhérent supprimé avec succès', 'success');
                            },
                            error: function(xhr, status, error) {
                                console.error('Error details:', {
                                    status: xhr.status,
                                    response: xhr.responseText,
                                    error: error
                                });

                                // Handle errors, show message to user
                                let errorMessage = 'Une erreur est survenue lors de la suppression';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                showToast(errorMessage, 'error');
                            }
                        });
                    });
                });
            }

            // Initial attachment of event handlers
            attachEventHandlers();

            // Add adherent button handler
            $('#addAdherent').click(function() {
                $('#adherentForm')[0].reset();
                $('#userId').val('');
                $('#adherentModal').modal('show');
            });

            // Save adherent handler
            $('#saveAdherent').click(function() {
                const formData = {
                    last_name: $('#lastName').val(),
                    first_name: $('#firstName').val(),
                    email: $('#email').val(),
                    email_imt: $('#email_imt').val(),
                    phone_number: $('#phone_number').val(),
                    group_id: $('#service').val(),
                    site_id: $('#site').val(),
                    status_id: $('#status').val(),
                    adhesion_valid: $('#adhesion').is(':checked') ? 1 : 0,
                    photo_release: $('#photo_release').is(':checked') ? 1 : 0,
                    photo_consent: $('#photo_consent').is(':checked') ? 1 : 0
                };

                // Debug log
                console.log('Form values:', {
                    site_id: $('#site').val(),
                    service: $('#service').val(),
                    status: $('#status').val()
                });
                console.log('Sending data:', formData);

                const userId = $('#userId').val();
                const url = userId ? `/adherents/${userId}` : '/adherents';
                const method = userId ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    success: function(response) {
                        console.log('Success:', response);
                        // Close the modal
                        $('#adherentModal').modal('hide');
                        // Show success message
                        showToast('Adhérent enregistré avec succès', 'success');
                        // Refresh the table or update the row
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error details:', {
                            status: xhr.status,
                            response: xhr.responseText,
                            error: error
                        });

                        // Handle errors, show message to user
                        let errorMessage = 'Une erreur est survenue lors de la sauvegarde';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage, 'error');
                    }
                });
            });

            function showToast(message, type = 'success') {
                const toast = $('#toast');
                const toastBody = toast.find('.toast-body');
                const toastHeader = toast.find('.toast-header');

                // Set the message
                toastBody.text(message);

                // Set the icon and color based on type
                if (type === 'success') {
                    toastHeader.find('i').removeClass().addClass('fa fa-check-circle me-2 text-success');
                } else if (type === 'error') {
                    toastHeader.find('i').removeClass().addClass('fa fa-exclamation-circle me-2 text-danger');
                }

                // Show the toast
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            }
        });
    </script>
@endsection
