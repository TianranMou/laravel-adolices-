document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuIcon = document.querySelector('.menu-icon');
    const menuContainer = document.getElementById('menu-container');
    const menuOverlay = document.getElementById('menu-overlay');

    menuToggle.addEventListener('click', function() {
        menuIcon.classList.toggle('open');
        menuContainer.classList.toggle('open');
        menuOverlay.classList.toggle('open');
    });

    menuOverlay.addEventListener('click', function() {
        menuIcon.classList.remove('open');
        menuContainer.classList.remove('open');
        menuOverlay.classList.remove('open');
    });
});
