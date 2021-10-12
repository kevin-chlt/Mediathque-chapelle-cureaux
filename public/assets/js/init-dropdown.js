document.addEventListener('DOMContentLoaded', function() {
    const elems = document.querySelectorAll('.dropdown-trigger');
    const options = {
        'alignment': 'center',
        'coverTrigger': false,
        'constrainWidth': false
    }
    const instances = M.Dropdown.init(elems, options);
});