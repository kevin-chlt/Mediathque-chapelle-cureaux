const select = document.querySelector('#filters_category');

const createPlaceholder = () => {
    const value = document.createElement('option')
    value.textContent = 'Filtrer par genre'
    value.setAttribute('selected', '') ;
    value.setAttribute('disabled', '') ;
    select.append(value);
}
createPlaceholder();