$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Function for confirmation (moved to global scope)
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
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { className: "text-center align-middle", targets: "_all" }
        ],
        createdRow: function(row, data, dataIndex) {
            // Get the adhesion status from column 6 (Adhesion column)
            const hasAdhesion = data[6] === 'Oui';
            $(row).attr('data-has-active-adhesion', hasAdhesion ? 'true' : 'false');
        },
        drawCallback: function() {
            // Reattach event handlers after each table redraw
            attachEventHandlers();
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
        $('tbody').append('<tr><td colspan="9" class="text-center">Chargement des données...</td></tr>');

        // If "all" is selected, use the initial data
        if (year === 'all') {
            const initialData = window.initialUsers || [];
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
                $('tbody').append('<tr><td colspan="9" class="text-center">Aucun utilisateur trouvé</td></tr>');
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
                    $('tbody').append('<tr><td colspan="9" class="text-center">Aucun utilisateur trouvé pour cette année</td></tr>');
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
                $('tbody').append('<tr><td colspan="9" class="text-center">Erreur lors du chargement des données</td></tr>');
            }
        });
    });

    function attachEventHandlers() {
        // Edit adherent button handler - using event delegation
        $(document).off('click', '.edit-adherent').on('click', '.edit-adherent', function() {
            const userId = $(this).data('id');

            // Fetch adherent data
            $.ajax({
                url: `/adherents/${userId}`,
                method: 'GET',
                success: function(response) {
                    console.log('Received adherent data:', response);

                    // Clear existing family members
                    $('#familyMembersContainer').empty();

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
                    } else if (response.sites && response.sites.length > 0) {
                        $('#site').val(response.sites[0].site_id);
                    }

                    $('#status').val(response.status_id);
                    $('#adhesion').prop('checked', response.adhesion_valid);
                    $('#photo_release').prop('checked', response.photo_release);
                    $('#photo_consent').prop('checked', response.photo_consent);

                    // Add family members if they exist
                    if (response.family_members && response.family_members.length > 0) {
                        response.family_members.forEach(function(member) {
                            // Format the birth date for the date input field
                            let birthDate = member.birth_date_member;
                            if (birthDate) {
                                // If the date is in a different format, convert it to YYYY-MM-DD
                                const date = new Date(birthDate);
                                if (!isNaN(date)) {
                                    birthDate = date.toISOString().split('T')[0];
                                }
                            }

                            const memberHtml = `
                                <div class="family-member mb-3" data-member-id="${member.member_id}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="card-title mb-0">Membre de la famille</h6>
                                                <button type="button" class="btn btn-danger btn-sm remove-family-member">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Nom</label>
                                                    <input type="text" class="form-control family-member-name" value="${member.name_member}" required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Prénom</label>
                                                    <input type="text" class="form-control family-member-firstname" value="${member.first_name_member}" required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Date de naissance</label>
                                                    <input type="date" class="form-control family-member-birthdate" value="${birthDate}" required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Relation</label>
                                                    <select class="form-select family-member-relation" required>
                                                        <option value="1" ${member.relation_id === 1 ? 'selected' : ''}>Enfant</option>
                                                        <option value="2" ${member.relation_id === 2 ? 'selected' : ''}>Conjoint(e)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#familyMembersContainer').append(memberHtml);
                        });
                    }

                    // Show the modal
                    $('#adherentModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching adherent data:', error);
                    showToast('Une erreur est survenue lors du chargement des données de l\'adhérent', 'error');
                }
            });
        });

        // Delete handler - using event delegation
        $(document).off('click', '.delete-adherent').on('click', '.delete-adherent', function() {
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
            photo_consent: $('#photo_consent').is(':checked') ? 1 : 0,
            family_members: []
        };

        // Collect family members data
        $('.family-member').each(function() {
            const memberData = {
                name_member: $(this).find('.family-member-name').val(),
                first_name_member: $(this).find('.family-member-firstname').val(),
                birth_date_member: $(this).find('.family-member-birthdate').val(),
                relation_id: parseInt($(this).find('.family-member-relation').val())
            };

            formData.family_members.push(memberData);
        });

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
                    error: error,
                    formData: formData
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

    // Add family member button handler
    $('#addFamilyMember').click(function() {
        const memberId = Date.now(); // Unique ID for the new member
        const memberHtml = `
            <div class="family-member mb-3" data-member-id="${memberId}">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title mb-0">Membre de la famille</h6>
                            <button type="button" class="btn btn-danger btn-sm remove-family-member">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control family-member-name" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control family-member-firstname" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" class="form-control family-member-birthdate" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Relation</label>
                                <select class="form-select family-member-relation" required>
                                    <option value="1">Enfant</option>
                                    <option value="2">Conjoint(e)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#familyMembersContainer').append(memberHtml);
    });

    // Remove family member handler
    $(document).on('click', '.remove-family-member', function() {
        const memberElement = $(this).closest('.family-member');
        const memberId = memberElement.data('member-id');
        const userId = $('#userId').val();

        if (userId) {
            // If we're editing an existing adherent
            showConfirmation('Êtes-vous sûr de vouloir supprimer ce membre de la famille ?', function() {
                $.ajax({
                    url: `/adherents/${userId}/family-members/${memberId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        memberElement.remove();
                        showToast('Membre de la famille supprimé avec succès', 'success');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting family member:', error);
                        showToast('Une erreur est survenue lors de la suppression du membre de la famille', 'error');
                    }
                });
            });
        } else {
            // If we're creating a new adherent, just remove from UI
            memberElement.remove();
        }
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
