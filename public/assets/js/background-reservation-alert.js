const status = document.querySelectorAll('#status');
for(let i = 0; i < status.length; i++){
    if(status[i].textContent.includes('Dépassement')){
        status[i].parentNode.style.background = 'rgba(255,0,0,0.3)';
    }
}