// Add class alert-reservation when status contain 'depassement'
const statuCheck = () => {
    const status = document.querySelectorAll('#status');
    for(let i = 0; i < status.length; i++){
        if(status[i].textContent.includes('Dépassement')){
            status[i].parentNode.classList.add('alert-reservation')
        }
    }
}

statuCheck();