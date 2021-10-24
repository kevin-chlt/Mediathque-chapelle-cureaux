const input = document.getElementById('registration_form_oldPassword');
const btn = document.getElementById('submitForm');

input.addEventListener('keyup', (e) => {
    if (e.target.value.length >= 1 ) {
        btn.classList.remove('disabled');
    } else {
        btn.classList.add('disabled');
    }
})