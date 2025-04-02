document.addEventListener('DOMContentLoaded', function() {
    let editorInitialized = false;

    // Initialize TinyMCE only for email content
    tinymce.init({
        selector: '#contentf',
        menubar: false,
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | link image media code',
        plugins: 'link image media code',
        setup: function(editor) {
            editor.on('init', function() {
                editorInitialized = true;
            });
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Function to load template into editor
    window.loadTemplateIntoEditor = function(select) {
        const content = select.value;
        const subject = select.options[select.selectedIndex].text;

        if (content) {
            // Wait for TinyMCE to be initialized
            if (editorInitialized && tinymce.get('contentf')) {
                tinymce.get('contentf').setContent(content);
                document.getElementById('subject').value = subject;
            } else {
                // If TinyMCE is not ready, wait and try again
                setTimeout(() => {
                    if (tinymce.get('contentf')) {
                        tinymce.get('contentf').setContent(content);
                        document.getElementById('subject').value = subject;
                    } else {
                        console.error('TinyMCE editor not initialized');
                    }
                }, 500);
            }
        }
    };

    // Communication type toggle (email or rocket chat)
    function toggleCommunicationType() {
        const emailSelected = document.getElementById('communication_type_email')?.checked || false;
        const rocketSelected = document.getElementById('communication_type_rocket')?.checked || false;

        const emailSection = document.getElementById('email-section');
        const rocketSection = document.getElementById('rocket-chat-section');
        const templateSection = document.getElementById('template-section');
        const subjectSection = document.getElementById('subject-section');
        const emailContentSection = document.getElementById('email-content-section');
        const rocketContentSection = document.getElementById('rocket-content-section');

        const submitButton = document.getElementById('submit-button');

        // Change submit button text based on selection
        if (submitButton) {
            submitButton.textContent = emailSelected ? 'Prévisualiser' : 'Envoyer directement';
        }

        // Show/hide sections based on selection
        if (emailSection) emailSection.style.display = emailSelected ? 'block' : 'none';
        if (rocketSection) rocketSection.style.display = rocketSelected ? 'block' : 'none';
        if (templateSection) templateSection.style.display = emailSelected ? 'block' : 'none';
        if (subjectSection) subjectSection.style.display = emailSelected ? 'block' : 'none';
        if (emailContentSection) emailContentSection.style.display = emailSelected ? 'block' : 'none';
        if (rocketContentSection) rocketContentSection.style.display = rocketSelected ? 'block' : 'none';

        // Toggle input requirements
        if (subjectSection) {
            const subjectInput = document.getElementById('subject');
            if (subjectInput) subjectInput.required = emailSelected;
        }

        // Handle content conversion between formats when switching
        if (emailSelected) {
            // Convert plain text to HTML if coming from rocket chat
            const rocketContent = document.getElementById('rocket_content')?.value || '';
            if (rocketContent && tinymce.get('contentf')) {
                // Simple plain text to HTML conversion
                tinymce.get('contentf').setContent(rocketContent.replace(/\n/g, '<br>'));
            }
        } else if (rocketSelected) {
            // Extract text from HTML if coming from email
            const emailContentHtml = tinymce.get('contentf')?.getContent() || '';
            if (emailContentHtml) {
                // Basic HTML to text conversion
                let textContent = emailContentHtml
                    .replace(/<br\s*\/?>/gi, '\n')
                    .replace(/<\/p><p>/gi, '\n\n')
                    .replace(/<[^>]*>/g, '');

                // Decode HTML entities
                const textarea = document.createElement('textarea');
                textarea.innerHTML = textContent;
                textContent = textarea.value;

                document.getElementById('rocket_content').value = textContent;
            }
        }

        // Toggle required attributes based on selection
        const emailFields = document.querySelectorAll('#email-section input[required]');
        emailFields.forEach(field => {
            field.required = emailSelected;
        });
    }

    // Add event listeners to communication type radio buttons
    document.getElementById('communication_type_email')?.addEventListener('change', toggleCommunicationType);
    document.getElementById('communication_type_rocket')?.addEventListener('change', toggleCommunicationType);

    // Initialize communication type visibility on page load
    toggleCommunicationType();

    // Update form submission handling
    document.getElementById('communication-form')?.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const emailSelected = document.getElementById('communication_type_email')?.checked || false;
        const rocketSelected = document.getElementById('communication_type_rocket')?.checked || false;
        let isValid = true;

        if (emailSelected) {
            // Email validation
            const subject = document.getElementById('subject');
            if (subject && !subject.value.trim()) {
                isValid = false;
                subject.classList.add('is-invalid');
            } else if (subject) {
                subject.classList.remove('is-invalid');
            }

            // Validate email content
            const editorContent = tinymce.get('contentf')?.getContent() || '';
            if (!editorContent.trim()) {
                isValid = false;
                document.querySelector('[for="contentf"]')?.classList.add('text-danger');
                document.getElementById('contentf')?.classList.add('is-invalid');
            } else {
                document.querySelector('[for="contentf"]')?.classList.remove('text-danger');
                document.getElementById('contentf')?.classList.remove('is-invalid');
            }

            // Validate email addresses
            const emailInputs = document.querySelectorAll('input[name="email_addresses[]"]');
            let hasValidEmail = false;

            emailInputs.forEach(input => {
                if (input.value.trim()) {
                    hasValidEmail = true;
                }
            });

            if (!hasValidEmail) {
                isValid = false;
                document.querySelector('[for="email-address"]')?.classList.add('text-danger');
                emailInputs[0].classList.add('is-invalid');
            }

            if (isValid) {
                // For email, use regular form submission with preview
                this.submit();
            }
        } else if (rocketSelected) {
            // RocketChat validation
            const rocketContent = document.getElementById('rocket_content')?.value || '';
            if (!rocketContent.trim()) {
                isValid = false;
                document.querySelector('[for="rocket_content"]')?.classList.add('text-danger');
                document.getElementById('rocket_content')?.classList.add('is-invalid');
            } else {
                document.querySelector('[for="rocket_content"]')?.classList.remove('text-danger');
                document.getElementById('rocket_content')?.classList.remove('is-invalid');
            }

            // Validate RocketChat destination
            let hasValidDestination = false;
            const channelSelected = document.getElementById('rocket_chat_channel')?.checked || false;
            const userSelected = document.getElementById('rocket_chat_user')?.checked || false;

            if (channelSelected) {
                const channelName = document.getElementById('rocket_chat_channel_name')?.value || '';
                if (channelName.trim()) {
                    hasValidDestination = true;
                } else {
                    document.getElementById('rocket_chat_channel_name')?.classList.add('is-invalid');
                }
            } else if (userSelected) {
                const username = document.getElementById('rocket_chat_username')?.value || '';
                if (username.trim()) {
                    hasValidDestination = true;
                } else {
                    document.getElementById('rocket_chat_username')?.classList.add('is-invalid');
                }
            }

            if (!hasValidDestination) {
                isValid = false;
            }

            if (isValid) {
                // For RocketChat, submit to a different endpoint for direct sending
                const formData = new FormData(this);
                formData.append('communication_type', 'rocket');
                formData.append('content', rocketContent);

                // Show loading state
                const submitButton = document.getElementById('submit-button');
                const originalText = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Envoi en cours...';

                // Send AJAX request
                const requestData = {
                    type: channelSelected ? 'channel' : 'user',
                    channel: channelSelected ? document.getElementById('rocket_chat_channel_name')?.value || '' : null,
                    username: userSelected ? document.getElementById('rocket_chat_username')?.value || '' : null,
                    message: rocketContent
                };

                fetch(this.dataset.rocketchatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(requestData)
                })
                .then(response => {
                    // Check if the response is a redirect to login
                    if (response.redirected) {
                        throw new Error('Vous devez être connecté pour envoyer des messages');
                    }

                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                const err = JSON.parse(text);
                                throw new Error(err.message || 'Network response was not ok');
                            } catch (e) {
                                // If the response contains HTML (likely a login page), throw auth error
                                if (text.includes('<!DOCTYPE html>')) {
                                    throw new Error('Vous devez être connecté pour envoyer des messages');
                                }
                                throw new Error(`Server error: ${response.status} - ${text}`);
                            }
                        });
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Invalid JSON response: ${text}`);
                        }
                    });
                })
                .then(data => {
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;

                    // Show result message
                    if (data.success) {
                        showNotification('success', data.message || 'Message Rocket Chat envoyé avec succès');
                        // Optionally clear form
                        document.getElementById('rocket_content').value = '';
                    } else {
                        showNotification('danger', 'Erreur lors de l\'envoi du message: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;

                    // Handle network errors
                    if (error.name === 'TypeError' && error.message === 'Load failed') {
                        showNotification('danger', 'Erreur de connexion au serveur. Veuillez vérifier votre connexion internet.');
                    }
                    // If it's an authentication error, redirect to login
                    else if (error.message.includes('connecté')) {
                        window.location.href = '/login';
                    } else {
                        showNotification('danger', 'Erreur technique lors de l\'envoi du message: ' + error.message);
                    }
                });
            }
        }
    });

    // Bouton pour sauvegarder directement le template sans modal
    const saveTemplateBtn = document.getElementById('save-template-btn');
    if (saveTemplateBtn) {
        saveTemplateBtn.addEventListener('click', function() {
            const subject = document.getElementById('subject').value.trim();
            const content = tinymce.get('contentf').getContent();

            if (!subject) {
                showSaveTemplateModal('warning', 'Veuillez saisir un sujet pour le template');
                document.getElementById('subject').classList.add('is-invalid');
                document.getElementById('subject').focus();
                return;
            }

            document.getElementById('subject').classList.remove('is-invalid');

            // Récupérer le token CSRF depuis les méta-tags
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Envoyer la requête AJAX
            fetch('/communiquer/template/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    subject: subject,
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                // Afficher le modal de confirmation
                const modalTitle = document.getElementById('templateSaveModalLabel');
                modalTitle.textContent = data.success ? 'Succès' : 'Erreur';

                showSaveTemplateModal(
                    data.success ? 'success' : 'danger',
                    data.message || (data.success ? 'Template enregistré avec succès' : 'Erreur lors de l\'enregistrement')
                );

                // Si succès, mettre à jour la liste des templates
                if (data.success && data.template) {
                    updateTemplateList(data.template);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showSaveTemplateModal('danger', 'Une erreur est survenue lors de la sauvegarde du template');
            });
        });
    }

    // Fonction pour afficher le modal de confirmation de sauvegarde du template
    function showSaveTemplateModal(type, message) {
        const modalBody = document.getElementById('templateSaveModalBody');
        const modalTitle = document.getElementById('templateSaveModalLabel');

        // Définir le titre selon le type de message
        if (type === 'success') {
            modalTitle.textContent = 'Succès';
            modalTitle.className = 'modal-title text-success';
        } else if (type === 'danger' || type === 'warning') {
            modalTitle.textContent = 'Erreur';
            modalTitle.className = 'modal-title text-danger';
        }

        // Ajouter un icône en fonction du type
        let icon = '';
        if (type === 'success') {
            icon = '<i class="fas fa-check-circle text-success me-2"></i>';
        } else if (type === 'danger') {
            icon = '<i class="fas fa-exclamation-circle text-danger me-2"></i>';
        } else if (type === 'warning') {
            icon = '<i class="fas fa-exclamation-triangle text-warning me-2"></i>';
        }

        modalBody.innerHTML = icon + message;

        // Afficher le modal de façon sûre
        try {
            const modal = document.getElementById('templateSaveModal');
            if (typeof bootstrap !== 'undefined') {
                // Méthode Bootstrap 5
                const templateSaveModal = new bootstrap.Modal(modal);
                templateSaveModal.show();
            } else {
                // Fallback - afficher manuellement
                console.warn("Bootstrap n'est pas défini. Utilisez un fallback pour afficher le modal.");
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');

                // Ajouter des gestionnaires pour fermer le modal
                const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
                closeButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                    });
                });
            }
        } catch (error) {
            console.error('Erreur lors de l\'affichage du modal:', error);
            // Si tout échoue, afficher une alerte
            alert(message);
        }
    }

    // Fonction pour mettre à jour la liste des templates
    function updateTemplateList(template) {
        const select = document.getElementById('template_select');
        if (select) {
            const option = document.createElement('option');
            option.value = template.content;
            option.text = template.subject;
            select.add(option);

            // Sélectionner le nouveau template
            select.value = template.content;
        }
    }

    // Fonction pour afficher une notification
    function showNotification(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        const container = document.querySelector('#communication-content');
        container.insertBefore(alertDiv, container.firstChild);

        // Auto-supprimer la notification après 5 secondes
        setTimeout(() => {
            if (alertDiv.parentNode) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alertDiv);
                    bsAlert.close();
                } else {
                    alertDiv.remove();
                }
            }
        }, 5000);
    }

    // Rocket Chat target selection toggle
    function toggleRocketChatTargets() {
        const channelSelected = document.getElementById('rocket_chat_channel')?.checked || false;
        const userSelected = document.getElementById('rocket_chat_user')?.checked || false;

        const channelSection = document.getElementById('channel-selection');
        const userSection = document.getElementById('user-selection');

        if (channelSection) channelSection.style.display = channelSelected ? 'block' : 'none';
        if (userSection) userSection.style.display = userSelected ? 'block' : 'none';
    }

    // Add event listeners to radio buttons
    document.getElementById('rocket_chat_channel')?.addEventListener('change', toggleRocketChatTargets);
    document.getElementById('rocket_chat_user')?.addEventListener('change', toggleRocketChatTargets);

    // Initialize visibility on page load
    toggleRocketChatTargets();

    window.addEmailField = function() {
        const container = document.getElementById('email-container');
        const existingFields = container.querySelectorAll('input[name="email_addresses[]"]');

        if (existingFields.length > 0) {
            const lastField = existingFields[existingFields.length - 1];
            if (!lastField.value.trim()) {
                lastField.classList.add('is-invalid');

                let errorMsg = document.getElementById('email-add-error');
                if (!errorMsg) {
                    errorMsg = document.createElement('div');
                    errorMsg.id = 'email-add-error';
                    errorMsg.className = 'alert alert-warning mt-2 mb-2';
                    errorMsg.role = 'alert';
                    container.appendChild(errorMsg);

                    setTimeout(() => {
                        if (errorMsg.parentNode) {
                            errorMsg.parentNode.removeChild(errorMsg);
                        }
                    }, 3000);
                }
                errorMsg.textContent = "Veuillez remplir le champ d'email précédent avant d'en ajouter un nouveau.";

                lastField.focus();
                return;
            }
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex mb-2';

        const input = document.createElement('input');
        input.type = 'email';
        input.name = 'email_addresses[]';
        input.className = 'form-control me-2';
        input.placeholder = 'Adresse email';
        input.required = true;

        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger';
        deleteBtn.innerHTML = '&times;';
        deleteBtn.onclick = function() {
            container.removeChild(wrapper);
        };

        wrapper.appendChild(input);
        wrapper.appendChild(deleteBtn);
        container.appendChild(wrapper);

        input.focus();
    };
});
