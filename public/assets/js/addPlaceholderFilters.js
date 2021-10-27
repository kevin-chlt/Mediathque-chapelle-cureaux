const select = document.querySelector('#filters_category');

const createPlaceholder = () => {
    const value = document.createElement('option')
    value.textContent = 'Genre'
    value.setAttribute('selected', '') ;
    value.setAttribute('disabled', '') ;
    select.append(value);
}
createPlaceholder();