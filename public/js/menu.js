document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuIcon = document.querySelector('.menu-icon');
    const menuContainer = document.getElementById('menu-container');
    const menuOverlay = document.getElementById('menu-overlay');
    const userSpace = document.getElementById('userSpace');
    const userMenu = document.getElementById('userMenu');
    const userOverlay = document.getElementById('user-overlay');

    // Toggle menu
    menuToggle.addEventListener('click', function() {
        menuIcon.classList.toggle('open');
        menuContainer.classList.toggle('open');
        menuOverlay.classList.toggle('open');
    });

    // Close menu when overlay is clicked
    menuOverlay.addEventListener('click', function() {
        menuIcon.classList.remove('open');
        menuContainer.classList.remove('open');
        menuOverlay.classList.remove('open');
    });

    // Toggle user menu
    userSpace.addEventListener('click', function(event) {
        event.stopPropagation(); // Prevent click from propagating to the document
        userMenu.classList.toggle('open');
        userOverlay.classList.toggle('open');
    });

    // Close user menu when clicking outside
    userOverlay.addEventListener('click', function() {
        userMenu.classList.remove('open');
        userOverlay.classList.remove('open');
    });
});
