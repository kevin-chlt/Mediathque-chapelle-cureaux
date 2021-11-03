const input1 = document.getElementById('password');
const btnShow1 = document.getElementById('view1');
const btnHide1 = document.getElementById('hide1');


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