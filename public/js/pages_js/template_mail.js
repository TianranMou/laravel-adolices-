$(document).ready(function() {
    // set csrf token for ajax requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showConfirmation(message, callback) {
        $('#confirmationMessage').text(message);
        $('#confirmationModal').modal('show');

        $('#confirmDelete').off('click');

        $('#confirmDelete').on('click', function() {
            $('#confirmationModal').modal('hide');
            callback();
        });
    }

    // datatable config init
    const table = $('#templateMailTable').DataTable({
        language: {
            "emptyTable": "Aucun template disponible",
            "info": "Affichage de _START_ à _END_ sur _TOTAL_ templates",
            "infoEmpty": "Affichage de 0 à 0 sur 0 template",
            "infoFiltered": "(filtré de _MAX_ templates au total)",
            "infoThousands": ",",
            "lengthMenu": "Afficher _MENU_ templates",
            "loadingRecords": "Chargement...",
            "processing": "Traitement...",
            "search": "Rechercher :",
            "zeroRecords": "Aucun template correspondant trouvé",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            }
        },
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true,
        columnDefs: [
            { className: "text-center align-middle", targets: -1 },
            {
                targets: 1,
                render: function(data, type, row) {
                    if (type === 'display') {
                        const maxLength = 100;
                        const displayText = $('<div>').html(data).text();
                        return displayText.length > maxLength ?
                            displayText.substring(0, maxLength) + '...' :
                            data;
                    }
                    return data;
                }
            }
        ]
    });

    $('#addTemplate').click(function() {
        $('#templateForm')[0].reset();
        $('#templateId').val('');
        $('#mail_content').val(''); // Explicitly clear content
        $('#templateModalLabel').text('Ajouter un template');
        $('#templateModal').modal('show');
    });

    $(document).on('click', '.edit-template', function() {
        const templateId = $(this).data('id');
        const row = $(this).closest('tr');

        $.ajax({
            url: `/template-mail/${templateId}`,
            method: 'GET',
            success: function(response) {
                console.log('Template data received:', response);

                $('#templateId').val(response.mail_template_id);
                $('#subject').val(response.subject);

                // Make sure content is properly set
                if (response.content) {
                    $('#mail_content').val(response.content);

                    // Check if content was set correctly
                    if ($('#mail_content').val() !== response.content) {
                        console.warn('Content not set correctly. Trying alternative approach.');
                        document.getElementById('mail_content').value = response.content;
                    }
                } else {
                    console.warn('Template content is empty or undefined');
                    $('#mail_content').val('');
                }

                $('#shop_id').val(response.shop_id || '');

                $('#templateModalLabel').text('Modifier un template');
                $('#templateModal').modal('show');

                // Store reference to the row being edited
                $('#saveTemplate').data('editing-row', row);
            },
            error: function(xhr) {
                console.error('Error fetching template:', xhr);
                showToast('Erreur lors du chargement du template', 'error');
            }
        });
    });

    $(document).on('click', '.delete-template', function() {
        const $btn = $(this);
        const templateId = $btn.data('id');
        const templateSubject = $btn.closest('tr').find('td:first').text();

        showConfirmation(`Êtes-vous sûr de vouloir supprimer le template "${templateSubject}" ?`, function() {
            $.ajax({
                url: `/template-mail/${templateId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        table.row($btn.closest('tr')).remove().draw();
                        showToast(response.message, 'success');
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Erreur lors de la suppression du template', 'error');
                }
            });
        });
    });

    $('#saveTemplate').click(function() {
        const templateId = $('#templateId').val();
        const shopIdValue = $('#shop_id').val();

        const formData = {
            subject: $('#subject').val(),
            content: $('#mail_content').val(),
            shop_id: shopIdValue === "" ? null : shopIdValue
        };

        const url = templateId ? `/template-mail/${templateId}` : '/template-mail';
        const method = templateId ? 'PUT' : 'POST';
        const editingRow = $(this).data('editing-row');

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#templateModal').modal('hide');

                    if (method === 'PUT' && editingRow) {
                        const rowData = table.row(editingRow).data();
                        rowData[0] = formData.subject;

                        let shopName = 'Aucune';
                        if (formData.shop_id) {
                            shopName = $('#shop_id option:selected').text();
                        }
                        rowData[1] = shopName;

                        table.row(editingRow).data(rowData).draw();
                    } else if (method === 'POST') {
                        const newId = response.template.mail_template_id;
                        let shopName = 'Aucune';
                        if (formData.shop_id) {
                            shopName = $('#shop_id option:selected').text();
                        }

                        const actions = `
                            <button class="btn btn-sm btn-primary edit-template" data-id="${newId}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-template" data-id="${newId}">
                                <i class="fa fa-trash"></i>
                            </button>
                        `;

                        table.row.add([formData.subject, shopName, actions]).draw();
                    }

                    showToast(response.message, 'success');
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Erreur lors de l\'enregistrement du template';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                showToast(errorMessage, 'error');
            }
        });
    });

    // Toast notification function
    function showToast(message, type = 'success') {
        const toast = $('#toast');
        const toastBody = toast.find('.toast-body');
        const toastHeader = toast.find('.toast-header i');

        toastBody.text(message);

        if (type === 'success') {
            toastHeader.removeClass().addClass('fa fa-check-circle me-2 text-success');
        } else {
            toastHeader.removeClass().addClass('fa fa-exclamation-circle me-2 text-danger');
        }

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }

    // Add debug logging to help diagnose content issues
    function addDebugLogging() {
        const originalVal = $.fn.val;

        $.fn.val = function(value) {
            const element = this[0];
            if (element && element.id === 'content') {
                if (arguments.length === 0) {
                    console.log('Reading content value:', originalVal.call(this));
                    return originalVal.call(this);
                }
                console.log('Setting content value to:', value);
            }
            return originalVal.apply(this, arguments);
        };
    }

    // Uncomment this line for debugging content issues
    // addDebugLogging();
});
