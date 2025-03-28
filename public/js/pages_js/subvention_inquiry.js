document.addEventListener('DOMContentLoaded', function() {
    const alertElements = document.querySelectorAll('.alert-dismissible');

    alertElements.forEach(alertElement => {
        const closeButton = alertElement.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                alertElement.classList.remove('show');
                setTimeout(() => {
                    alertElement.remove();
                }, 150);
            });
        }
    });
});
