const input1 = document.getElementById('registration_form_plainPassword_first');
const btnShow1 = document.getElementById('view1');
const btnHide1 = document.getElementById('hide1')
const input2 = document.getElementById('registration_form_plainPassword_second');
const btnShow2 = document.getElementById('view2');
const btnHide2 = document.getElementById('hide2')


btnShow1.addEventListener('click', () => {
    input1.type = 'text';
    btnShow1.style.display = 'none';
    btnHide1.style.display = 'block';
})

btnHide1.addEventListener('click', () => {
    input1.type = 'password';
    btnShow1.style.display = 'block';
    btnHide1.style.display = 'none';
})

btnShow2.addEventListener('click', () => {
    input2.type = 'text';
    btnShow2.style.display = 'none';
    btnHide2.style.display = 'block';
})

btnHide2.addEventListener('click', () => {
    input2.type = 'password';
    btnShow2.style.display = 'block';
    btnHide2.style.display = 'none';
})

