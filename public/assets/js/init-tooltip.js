document.addEventListener('DOMContentLoaded', function() {
    let elems = document.querySelectorAll('.tooltipped');
    let instances = M.Tooltip.init(elems, {
        'exitDelay': 1000,
        'html': '<span class="tooltip-text">Vous pouvez vous connecter pour tester <br> le site avec les identifiants suivant <br>' +
            '<u>Mail:</u> admin@admin.fr <br> <u>Pass:</u> administrator <span>'
    });
});