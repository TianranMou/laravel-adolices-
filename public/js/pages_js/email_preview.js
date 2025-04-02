document.addEventListener('DOMContentLoaded', function() {
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const modifyEmailBtn = document.getElementById('modifyEmailBtn');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmEmailModal'));

    sendEmailBtn.addEventListener('click', function(e) {
        e.preventDefault();
        confirmModal.show();
    });

    modifyEmailBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // Rediriger vers la page de communication avec un paramètre
        // indiquant qu'on revient de la prévisualisation
        window.location.href = '/communiquer?from_preview=1';
    });
});
