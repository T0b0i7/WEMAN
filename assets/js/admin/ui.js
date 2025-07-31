class AdminUI {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.mainContent = document.querySelector('.main-content');
        this.init();
    }

    init() {
        this.initSidebar();
        this.initResponsiveLayout();
        this.initNotifications();
        this.initDataTables();
        this.initFormValidation();
    }

    // Gestion du sidebar
    initSidebar() {
        const toggleBtns = document.querySelectorAll('.sidebar-toggle');
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', () => this.toggleSidebar());
        });

        // Fermeture automatique sur mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 991.98 && 
                !e.target.closest('.sidebar') && 
                !e.target.closest('.sidebar-toggle')) {
                this.sidebar?.classList.remove('active');
            }
        });
    }

    toggleSidebar() {
        this.sidebar?.classList.toggle('active');
        if (window.innerWidth > 991.98) {
            this.mainContent.style.marginLeft = 
                this.sidebar?.classList.contains('active') ? '0' : '250px';
        }
    }

    // Gestion du responsive
    initResponsiveLayout() {
        const resizeObserver = new ResizeObserver(entries => {
            for (const entry of entries) {
                if (entry.target === document.documentElement) {
                    this.handleResize();
                }
            }
        });

        resizeObserver.observe(document.documentElement);
        this.handleResize();
    }

    handleResize() {
        if (window.innerWidth <= 991.98) {
            this.sidebar?.classList.remove('active');
            this.mainContent.style.marginLeft = '0';
        } else {
            this.mainContent.style.marginLeft = '250px';
        }
    }

    // Système de notifications
    initNotifications() {
        this.initNotificationContainer();
    }

    initNotificationContainer() {
        this.notificationContainer = document.createElement('div');
        this.notificationContainer.className = 'notification-container';
        document.body.appendChild(this.notificationContainer);
    }

    showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} show`;
        notification.innerHTML = `
            <i class="fas fa-${this.getNotificationIcon(type)} me-2"></i>
            ${message}
        `;

        this.notificationContainer.appendChild(notification);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || icons.info;
    }

    // Initialisation des DataTables
    initDataTables() {
        const tables = document.querySelectorAll('.datatable');
        tables.forEach(table => {
            new DataTable(table, {
                responsive: true,
                language: {
                    url: '/assets/js/admin/datatable-fr.json'
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });
        });
    }

    // Validation des formulaires
    initFormValidation() {
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    }
}

// Export de l'instance unique
export const adminUI = new AdminUI();

document.addEventListener('DOMContentLoaded', () => {
    // Toggle menu mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });

        // Fermer le menu au clic en dehors
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
});