
function toggleEmailFields() {
    const emailType = document.getElementById('email_type').value;
    const emailField = document.getElementById('email');
    const emailImtField = document.getElementById('email_imt');
    const emailImtContainer = document.getElementById('email_imt_container');
    const emailAlsoCheckbox = document.getElementById('email_also_checkbox');
    const emailAlsoContainer = document.getElementById('email_also_container');

    emailField.required = false;
    emailImtField.required = false;
    emailField.type = 'text';
    emailImtField.type = 'text';

    if (emailType === 'email') {
        // emailField.required = true;
        // emailImtField.required = false;

        // emailField.type = 'email';
        // emailImtField.type = 'text';

        emailImtField.value = '';
        emailImtContainer.style.display = 'none';
        emailAlsoContainer.style.display = 'none';
        emailAlsoCheckbox.checked = false;
        emailField.parentNode.style.display = 'block';
    } else if (emailType === 'email_imt') {
        // emailField.required = false;
        // emailImtField.required = true;

        // emailField.type = 'text';
        // emailImtField.type = 'email';

        emailImtContainer.style.display = 'block';
        emailField.value = '';
        emailAlsoContainer.style.display = 'block';
        if(!emailAlsoCheckbox.checked){
            emailField.parentNode.style.display = 'none';
        }else{
            emailField.parentNode.style.display = 'block';
        }
    }
}
function toggleEmailAlso(){
    const emailField = document.getElementById('email');
    const emailAlsoCheckbox = document.getElementById('email_also_checkbox');
    if(emailAlsoCheckbox.checked){
        emailField.parentNode.style.display = 'block';
    }else{
        emailField.parentNode.style.display = 'none';
    }
}

// Initialize the email fields on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleEmailFields();
});