document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#contentf',
        menubar: false,
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | link image media code',
        plugins: 'link image media code',
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });

    // Add this to your communiquer.js file
    document.addEventListener('DOMContentLoaded', function() {
        // Initial setup of fields
        updateRocketChatFields();

        // Add event listeners to radio buttons
        document.querySelectorAll('input[name="rocket_chat_type"]').forEach(function(radio) {
            radio.addEventListener('change', updateRocketChatFields);
        });

        function updateRocketChatFields() {
            const channelSelected = document.getElementById('rocket_chat_channel').checked;
            const userSelected = document.getElementById('rocket_chat_user').checked;

            document.getElementById('channel-selection').style.display = channelSelected ? 'block' : 'none';
            document.getElementById('user-selection').style.display = userSelected ? 'block' : 'none';
        }
    });

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

    document.getElementById('communication-form')?.addEventListener('submit', function(e) {
        let isValid = true;

        // Validate editor content
        const editorContent = tinymce.get('contentf')?.getContent() || '';
        if (!editorContent.trim()) {
            isValid = false;
            document.querySelector('[for="content"]')?.classList.add('text-danger');
            document.getElementById('contentf')?.classList.add('is-invalid');
        } else {
            document.querySelector('[for="content"]')?.classList.remove('text-danger');
            document.getElementById('contentf')?.classList.remove('is-invalid');
        }

        // Validate subject
        const subject = document.getElementById('subject');
        if (subject && !subject.value.trim()) {
            isValid = false;
            subject.classList.add('is-invalid');
        } else if (subject) {
            subject.classList.remove('is-invalid');
        }

        // Validate Rocket Chat fields if selected
        const channelSelected = document.getElementById('rocket_chat_channel')?.checked || false;
        const userSelected = document.getElementById('rocket_chat_user')?.checked || false;

        if (channelSelected || userSelected) {
            e.preventDefault(); // Stop normal submit

            // Create FormData object from the form
            const formData = new FormData(this);

            // Remove existing rocket chat fields that don't match controller expectations
            formData.delete('rocket_chat_channel_name');
            formData.delete('rocket_chat_username');

            // Fix the type value based on selection
            if (userSelected) {
                formData.set('rocket_chat_type', 'direct');

                // Add username as array element
                const username = document.getElementById('rocket_chat_username').value;
                formData.append('rocket_users[]', username);
            }

            if (channelSelected) {
                formData.set('rocket_chat_type', 'channel');

                // Add channel as array element
                const channelName = document.getElementById('rocket_chat_channel_name').value;
                formData.append('rocket_channels[]', channelName);
            }

            // Add debugging - check browser console after submitting
            console.log('Sending Rocket Chat data:', {
                type: formData.get('rocket_chat_type'),
                channels: formData.getAll('rocket_channels[]'),
                users: formData.getAll('rocket_users[]')
            });

            // Submit the form
            fetch(this.action, {
                method: 'POST',
                body: formData
            }).then(response => {
                console.log('Response status:', response.status);
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            }).then(html => {
                if (html) {
                    document.open();
                    document.write(html);
                    document.close();
                }
            }).catch(error => {
                console.error('Fetch error:', error);
            });
        }

        if (!isValid) {
            e.preventDefault();
        }

        return isValid;
    });

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

    window.loadTemplateIntoEditor = function(selectElement) {
        const templateContent = selectElement.value;
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const templateSubject = selectedOption.text;

        if (templateContent) {
            tinymce.get('contentf').setContent(templateContent);
            document.getElementById('subject').value = templateSubject;
        }
    };
});
