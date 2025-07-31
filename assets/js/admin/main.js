document.addEventListener('DOMContentLoaded', function() {
    // Toggle Sidebar
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    sidebarToggle.forEach(toggle => {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (window.innerWidth > 991.98) {
                mainContent.style.marginLeft = sidebar.classList.contains('active') ? '0' : '250px';
            }
        });
    });

    // Fermer le sidebar sur mobile lors du clic en dehors
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98 && 
            !e.target.closest('.sidebar') && 
            !e.target.closest('.sidebar-toggle')) {
            sidebar.classList.remove('active');
        }
    });

    // Gestion des tableaux responsives
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        if (!table.closest('.table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}); 