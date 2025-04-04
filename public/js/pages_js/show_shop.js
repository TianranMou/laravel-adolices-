document.addEventListener('DOMContentLoaded', function() {
    const modalButtons = document.querySelectorAll('.custom-modal-trigger');

    modalButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.value;

            const modal = document.getElementById(`purchaseModal${productId}`);

            if (typeof bootstrap !== 'undefined') {
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();
            } else {
                console.warn("Bootstrap n'est pas défini. Utilisation d'un fallback pour afficher la modale.");
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');

                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);

                const closeButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
                closeButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        backdrop.remove();
                    });
                });
            }
        });
    });

    const purchaseForms = document.querySelectorAll('form[id^="purchaseForm"]');

    purchaseForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const regularQuantity = parseInt(form.querySelector('input[name="regular_quantity"]').value) || 0;
            const subsidizedQuantity = parseInt(form.querySelector('input[name="subsidized_quantity"]').value) || 0;
            const productId = form.querySelector('input[name="product_id"]').value;
            const validationMessage = document.getElementById(`validation-message${productId}`);

            if (regularQuantity === 0 && subsidizedQuantity === 0) {
                e.preventDefault();
                validationMessage.style.display = 'block';
                return false;
            }

            validationMessage.style.display = 'none';
            return true;
        });
    });

    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            const productId = form.querySelector('input[name="product_id"]').value;
            const validationMessage = document.getElementById(`validation-message${productId}`);
            validationMessage.style.display = 'none';
        });
    });

    if(typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
