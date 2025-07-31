document.addEventListener('DOMContentLoaded', function() {
    // Gérer l'état actif du menu
    const currentPage = window.location.pathname.split('/').pop().split('.')[0];
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(currentPage)) {
            link.classList.add('active');
        }
    });

    // Gérer l'état actif des items du dropdown
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    
    if (category) {
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            if (item.href.includes(category)) {
                item.classList.add('active');
            }
        });
    }

    // Gestion du menu principal
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // Fonction pour fermer tous les dropdowns sauf celui spécifié
    const closeOtherDropdowns = (exceptDropdown) => {
        dropdowns.forEach(dropdown => {
            if (dropdown !== exceptDropdown) {
                dropdown.classList.remove('show');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) menu.classList.remove('show');
            }
        });
    };

    // Gestion des clics sur les dropdown toggles
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (toggle && menu) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Fermer les autres dropdowns
                closeOtherDropdowns(dropdown);

                // Toggle le dropdown actuel
                dropdown.classList.toggle('show');
                menu.classList.toggle('show');
            });

            // Empêcher la fermeture lors du clic dans le menu
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });

    // Gestion des liens dans les dropdowns
    document.querySelectorAll('.dropdown-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Ne pas empêcher la navigation si c'est un lien normal
            if (!this.classList.contains('dropdown-toggle')) {
                const dropdown = this.closest('.dropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                    const menu = dropdown.querySelector('.dropdown-menu');
                    if (menu) menu.classList.remove('show');
                }
            } else {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // Fermer les dropdowns lors d'un clic en dehors
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('show');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) menu.classList.remove('show');
            });
        }
    });

    // Gestion du menu mobile
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            navbarCollapse.classList.toggle('show');
        });

        // Fermer le menu mobile lors du clic sur un lien
        document.querySelectorAll('.navbar-nav a:not(.dropdown-toggle)').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    navbarCollapse.classList.remove('show');
                }
            });
        });
    }

    // Gestion du scroll
    let lastScroll = 0;
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > lastScroll && currentScroll > 100) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }

        if (currentScroll > 100) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }

        lastScroll = currentScroll;
    });
}); 