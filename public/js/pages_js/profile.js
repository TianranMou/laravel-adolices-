document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.getElementById('edit-button');
    const profileForm = document.getElementById('profile-form');
    const inputs = profileForm.querySelectorAll('input, select');

    editButton.addEventListener('click', function () {
        if (editButton.textContent === 'Modifier') {
            inputs.forEach(input => input.disabled = false);
            editButton.textContent = 'Enregistrer';
            editButton.classList.remove('btn-primary');
            editButton.classList.add('btn-success');
        } else {
            profileForm.submit();
        }
    });
});
