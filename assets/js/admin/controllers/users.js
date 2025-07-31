import { userService } from '../services/users.js';
import { notifications } from '../services/notifications.js';

class UserController {
    constructor() {
        this.table = null;
        this.init();
    }

    async init() {
        try {
            await this.initDataTable();
            this.setupEventListeners();
            this.updateStats();
        } catch (error) {
            console.error('Erreur d\'initialisation:', error);
        }
    }

    setupEventListeners() {
        // Formulaire d'ajout/édition
        document.getElementById('userForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit(e.target);
        });

        // Actions sur les utilisateurs
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-action]');
            if (!target) return;

            const { action, userId } = target.dataset;
            switch (action) {
                case 'edit':
                    this.editUser(userId);
                    break;
                case 'delete':
                    this.deleteUser(userId);
                    break;
                case 'view':
                    this.viewUser(userId);
                    break;
            }
        });

        // Filtres
        document.getElementById('roleFilter')?.addEventListener('change', () => this.applyFilters());
        document.getElementById('statusFilter')?.addEventListener('change', () => this.applyFilters());
        document.getElementById('searchInput')?.addEventListener('input', debounce(() => this.applyFilters(), 300));
    }

    async initDataTable() {
        const users = await userService.getAll();
        
        this.table = $('#usersTable').DataTable({
            data: users,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            columns: [
                { data: 'id' },
                { 
                    data: null,
                    render: data => `${data.firstName} ${data.lastName}`
                },
                { data: 'email' },
                { data: 'phone' },
                { 
                    data: 'role',
                    render: this.renderRole.bind(this)
                },
                { 
                    data: 'status',
                    render: this.renderStatus.bind(this)
                },
                {
                    data: null,
                    render: this.renderActions.bind(this)
                }
            ]
        });
    }

    async handleSubmit(form) {
        try {
            const formData = new FormData(form);
            const userData = Object.fromEntries(formData.entries());
            
            const errors = userService.validateUser(userData);
            if (errors.length) {
                notifications.error(errors.join('<br>'));
                return;
            }

            if (userData.id) {
                await userService.update(userData.id, userData);
            } else {
                await userService.create(userData);
            }

            this.refreshTable();
            this.updateStats();
            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
        } catch (error) {
            console.error('Erreur lors de la soumission:', error);
        }
    }

    async deleteUser(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            return;
        }

        try {
            await userService.delete(id);
            this.refreshTable();
            this.updateStats();
        } catch (error) {
            console.error('Erreur lors de la suppression:', error);
        }
    }

    async editUser(id) {
        try {
            const user = await userService.getById(id);
            const form = document.getElementById('userForm');
            
            Object.entries(user).forEach(([key, value]) => {
                const input = form.elements[key];
                if (input) input.value = value;
            });

            const modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        } catch (error) {
            console.error('Erreur lors de l\'édition:', error);
        }
    }

    async viewUser(id) {
        try {
            const user = await userService.getById(id);
            // Implémenter l'affichage des détails
            console.log('Détails utilisateur:', user);
        } catch (error) {
            console.error('Erreur lors de l\'affichage:', error);
        }
    }

    async refreshTable() {
        const users = await userService.getAll();
        this.table.clear();
        this.table.rows.add(users);
        this.table.draw();
    }

    async updateStats() {
        const stats = await userService.getStats();
        document.getElementById('totalUsers').textContent = stats.total;
        document.getElementById('activeUsers').textContent = stats.active;
        document.getElementById('pendingUsers').textContent = stats.pending;
    }

    // Méthodes de rendu
    renderRole(role) {
        const badges = {
            admin: 'danger',
            editor: 'warning',
            user: 'info'
        };
        return `<span class="badge bg-${badges[role] || 'secondary'}">${role}</span>`;
    }

    renderStatus(status) {
        const badges = {
            active: 'success',
            pending: 'warning',
            blocked: 'danger'
        };
        return `<span class="badge bg-${badges[status] || 'secondary'}">${status}</span>`;
    }

    renderActions(data) {
        return `
            <div class="btn-group btn-group-sm">
                <button class="btn btn-info" data-action="view" data-user-id="${data.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning" data-action="edit" data-user-id="${data.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger" data-action="delete" data-user-id="${data.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    }
}

// Utilitaire pour debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.userController = new UserController();
}); 