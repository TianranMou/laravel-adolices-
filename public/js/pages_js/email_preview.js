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
        window.location.href = '/communiquer';
    });
});
