const input = document.getElementById('search-input');
const dropdown = document.getElementById('dropdown-search');
const containerDropdown = document.querySelector('.dropdown-search-container');

input.addEventListener('keydown', async (e) => {
    let searchWord = input.value;

    if(input.value.length > 1) {
        let response = await fetch('http://localhost:8000/api/books');
        if(response.ok && response.status === 200) {
            let data = await response.json();
            const searchResult = await data.filter(book => book.title.includes(searchWord))
            await cleanDropdown()
            displayResults(searchResult)
        }
    }
})

const displayResults = (results) => {
    containerDropdown.style.display = 'inline-block';
    if(results.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'Aucun livre ne correspond à votre recherche'
        dropdown.append(li)
    } else {
        for(let i = 0; i < results.length; i++) {
            const li = document.createElement('li');
            li.insertAdjacentHTML('afterbegin', `<a href="http://localhost:8000/books/${results[i].id}"> ${results[i].title} </a>` )
            dropdown.append(li)
        }
    }
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