

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Sidebar toggle functionality
const sidebarToggleButton = document.getElementById('sidebar-toggle');
const sidebar = document.getElementById('nav-sidebar');

sidebarToggleButton.addEventListener('click', () => {
    const isExpanded = sidebarToggleButton.getAttribute('aria-expanded') === 'true';
    sidebarToggleButton.setAttribute('aria-expanded', !isExpanded);
    sidebar.classList.toggle('-translate-x-full');    
});