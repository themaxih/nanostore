document.addEventListener('DOMContentLoaded', function () {
    const retourHaut = document.getElementById('retour-haut');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 400) {
            retourHaut.style.opacity = '1';
            retourHaut.style.visibility = 'visible';
        } else {
            retourHaut.style.opacity = '0';
            retourHaut.style.visibility = 'hidden';
        }
    });
});