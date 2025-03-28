// Initialisation de TinyMCE
tinymce.init({
    selector: '#contentf',
    menubar: false,
    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright | link image media code',
    plugins: 'link image media code',
});

// Fonction pour charger le template dans TinyMCE
function loadTemplateIntoEditor(selectElement) {
    const templateContent = selectElement.value;  // Récupérer la valeur de l'option sélectionnée (HTML)
    if (templateContent) {
        // Insérer le code HTML du template dans l'éditeur TinyMCE
        tinymce.get('contentf').setContent(templateContent);
    }
}

// Fonction pour ajouter un champ email supplémentaire
function addEmailField() {
    const emailContainer = document.getElementById('email-container');
    const input = document.createElement('input');
    input.type = 'email';
    input.name = 'email_addresses[]';
    input.classList.add('form-control', 'mb-2');
    input.placeholder = 'Adresse Email';
    emailContainer.appendChild(input);
}
