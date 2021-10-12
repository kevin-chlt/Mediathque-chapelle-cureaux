document.addEventListener('DOMContentLoaded', function() {
    const elems = document.querySelectorAll('select');
    const options = {
        'isMultiple' : true,
    }
    const instances = M.FormSelect.init(elems, options);
});