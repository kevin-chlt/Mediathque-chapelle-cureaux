const input = document.getElementById('search-input');
const dropdown = document.getElementById('dropdown-search');
const containerDropdown = document.querySelector('.dropdown-search-container');
const loadingIcon = document.getElementById('loading-icon');


const search = async () => {
    let searchWord = input.value;
    await cleanDropdown();
    containerDropdown.style.display = 'flex';
    loadingIcon.style.display = 'inline-block';

    if (searchWord.length > 0 && searchWord.length <= 2) {
        displayHelpText('Continuez à taper ...');
    } else if (searchWord.length === 0) {
        containerDropdown.style.display = 'none';
        loadingIcon.style.display = 'none';
    }

    if(searchWord.length > 2){
        let response = await fetch('https://mediatheque-chapelle-cureaux.herokuapp.com/api/books');
        let data = await response.json();
        const searchResult = await data.filter(book => book.title.includes(searchWord));
        loadingIcon.style.display = 'none';

        if((response.ok && response.status === 200) || response.status === 304) {
            searchResult.length !== 0 ? searchResult.forEach((result) => displayResults(result)) : displayHelpText('Aucun titre ne ressemble à votre recherche.');
        } else {
            displayHelpText('Une erreur est survenue.');
        }
    }
}

input.addEventListener('keyup', search);

const displayResults = (content) => {
    const li = document.createElement('li');
    li.insertAdjacentHTML('afterbegin', `<a href="https://mediatheque-chapelle-cureaux.herokuapp.com/books/${content.id}"> ${content.title} </a>`);
    dropdown.append(li);
}

const displayHelpText = (content) => {
    const li = document.createElement('li');
    li.textContent = content;
    dropdown.append(li);
}

const cleanDropdown = () => {
    const dropdownLength = dropdown.children.length - 1
    for(let i = dropdownLength; i >= 0; i--) {
        dropdown.children[i].remove();
    }
}

document.addEventListener('click', (e) => {
    if(e.target.id !== 'dropdown-search') {
        containerDropdown.style.display = 'none';
    }
})