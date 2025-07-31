class UserManager {
    constructor() {
        this.users = [];
        this.currentUser = null;
        this.table = null;
        this.init();
    }

    async init() {
        try {
            await this.loadUsers();
            this.initDataTable();
            this.setupEventListeners();
        } catch (error) {
            console.error('Erreur d\'initialisation:', error);
        }
    }

    setupEventListeners() {
        // Formulaire d'ajout/édition
        document.getElementById('userForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveUser();
        });

        // Réinitialisation du formulaire à l'ouverture du modal
        const userModal = document.getElementById('userModal');
        if (userModal) {
            userModal.addEventListener('show.bs.modal', (e) => {
                const button = e.relatedTarget;
                const userId = button?.dataset.userId;
                this.resetForm();
                if (userId) {
                    this.loadUserData(userId);
                }
            });
        }
    }

    async loadUsers() {
        try {
            // Simulation de données
            this.users = [
                {
                    id: 1,
                    firstName: "John",
                    lastName: "Doe",
                    email: "john@example.com",
                    phone: "+225 0123456789",
                    role: "admin",
                    status: "active",
                    registrationDate: "2024-01-15"
                },
                {
                    id: 2,
                    firstName: "Jane",
                    lastName: "Smith",
                    email: "jane@example.com",
                    phone: "+225 9876543210",
                    role: "editor",
                    status: "pending",
                    registrationDate: "2024-02-01"
                }
            ];

            this.updateStats();
            return this.users;
        } catch (error) {
            console.error('Erreur de chargement:', error);
            this.showNotification('Erreur lors du chargement des utilisateurs', 'danger');
            return [];
        }
    }

    initDataTable() {
        if (!$.fn.DataTable) {
            console.error('DataTable n\'est pas chargé');
            return;
        }

        this.table = $('#usersTable').DataTable({
            data: this.users,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            columns: [
                { data: 'id' },
                { 
                    data: null,
                    render: function(data) {
                        return `${data.firstName} ${data.lastName}`;
                    }
                },
                { data: 'email' },
                { data: 'phone' },
                { 
                    data: 'role',
                    render: function(data) {
                        const badges = {
                            admin: 'danger',
                            editor: 'warning',
                            user: 'info'
                        };
                        return `<span class="badge bg-${badges[data]}">${data}</span>`;
                    }
                },
                { 
                    data: 'status',
                    render: function(data) {
                        const badges = {
                            active: 'success',
                            pending: 'warning',
                            blocked: 'danger'
                        };
                        return `<span class="badge bg-${badges[data]}">${data}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
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
            ]
        });
    }

    updateTable() {
        this.table.clear();
        this.table.rows.add(this.users);
        this.table.draw();

        // Ajouter les écouteurs d'événements pour les boutons de suppression
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
                    this.deleteUser(btn.dataset.userId);
                }
            });
        });
    }

    updateStats() {
        document.getElementById('totalUsers').textContent = this.users.length;
        document.getElementById('activeUsers').textContent = 
            this.users.filter(u => u.status === 'active').length;
        document.getElementById('pendingUsers').textContent = 
            this.users.filter(u => u.status === 'pending').length;
        document.getElementById('blockedUsers').textContent = 
            this.users.filter(u => u.status === 'blocked').length;
    }

    async saveUser() {
        const form = document.getElementById('userForm');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';

            const formData = new FormData(form);
            const userData = Object.fromEntries(formData.entries());

            // Simulation de sauvegarde
            await new Promise(resolve => setTimeout(resolve, 1000));

            if (userData.id) {
                // Mise à jour
                const index = this.users.findIndex(u => u.id === parseInt(userData.id));
                this.users[index] = { ...this.users[index], ...userData };
            } else {
                // Création
                userData.id = this.users.length + 1;
                userData.registrationDate = new Date().toISOString().split('T')[0];
                this.users.push(userData);
            }

            this.updateTable();
            this.updateStats();
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
            modal.hide();
            
            this.showNotification('Utilisateur enregistré avec succès', 'success');

        } catch (error) {
            this.showNotification('Erreur lors de l\'enregistrement', 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Enregistrer';
        }
    }

    async deleteUser(userId) {
        try {
            // Simulation de suppression
            await new Promise(resolve => setTimeout(resolve, 500));
            
            this.users = this.users.filter(u => u.id !== parseInt(userId));
            this.updateTable();
            this.updateStats();
            
            this.showNotification('Utilisateur supprimé avec succès', 'success');
        } catch (error) {
            this.showNotification('Erreur lors de la suppression', 'danger');
        }
    }

    loadUserData(userId) {
        const user = this.users.find(u => u.id === parseInt(userId));
        if (user) {
            const form = document.getElementById('userForm');
            Object.entries(user).forEach(([key, value]) => {
                const input = form.elements[key];
                if (input) input.value = value;
            });
        }
    }

    resetForm() {
        const form = document.getElementById('userForm');
        form.reset();
        form.elements['id'].value = '';
    }

    showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.appendChild(toast);
        document.body.appendChild(container);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            container.remove();
        });
    }
}

// Initialisation quand jQuery est chargé
$(document).ready(() => {
    window.userManager = new UserManager();
}); 