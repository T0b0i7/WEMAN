class CategoryManager {
    constructor() {
        this.categories = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadCategories();
    }

    setupEventListeners() {
        // Formulaire d'ajout/édition
        document.getElementById('categoryForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveCategory();
        });

        // Bouton d'ajout
        document.querySelector('.btn-add-category')?.addEventListener('click', () => {
            this.resetForm();
            const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
            modal.show();
        });
    }

    async loadCategories() {
        try {
            // Simulation de données pour le développement
            this.categories = [
                {
                    id: 1,
                    name: 'Exposés',
                    description: 'Exposés académiques',
                    icon: 'fa-graduation-cap',
                    color: '#0066cc',
                    documentCount: 25,
                    active: true
                },
                {
                    id: 2,
                    name: 'Mémoires',
                    description: 'Mémoires de fin d\'études',
                    icon: 'fa-book',
                    color: '#28a745',
                    documentCount: 15,
                    active: true
                }
            ];
            
            this.renderCategories();
            this.updateStats();
        } catch (error) {
            this.showNotification('Erreur lors du chargement des catégories', 'danger');
        }
    }

    renderCategories() {
        const container = document.querySelector('#categoriesList tbody');
        if (!container) return;

        container.innerHTML = this.categories.map(cat => `
            <tr>
                <td>
                    <i class="fas ${cat.icon}" style="color: ${cat.color}"></i>
                    ${cat.name}
                </td>
                <td>${cat.description}</td>
                <td>${cat.documentCount}</td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" 
                            ${cat.active ? 'checked' : ''} 
                            onchange="categoryManager.toggleStatus(${cat.id}, this.checked)">
                    </div>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="categoryManager.editCategory(${cat.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="categoryManager.deleteCategory(${cat.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    updateStats() {
        const stats = {
            total: this.categories.length,
            active: this.categories.filter(c => c.active).length,
            documents: this.categories.reduce((sum, cat) => sum + cat.documentCount, 0)
        };

        Object.entries(stats).forEach(([key, value]) => {
            const el = document.querySelector(`#${key}Count`);
            if (el) el.textContent = value;
        });
    }

    async saveCategory() {
        const form = document.getElementById('categoryForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';

            const categoryData = {
                id: formData.get('id') || Date.now(),
                name: formData.get('name'),
                description: formData.get('description'),
                icon: formData.get('icon'),
                color: formData.get('color'),
                active: formData.get('active') === 'on'
            };

            // Simulation de sauvegarde
            await new Promise(resolve => setTimeout(resolve, 1000));

            if (formData.get('id')) {
                // Mise à jour
                const index = this.categories.findIndex(c => c.id == categoryData.id);
                this.categories[index] = { ...this.categories[index], ...categoryData };
            } else {
                // Nouvelle catégorie
                categoryData.documentCount = 0;
                this.categories.push(categoryData);
            }

            this.renderCategories();
            this.updateStats();
            
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            this.showNotification('Catégorie enregistrée avec succès', 'success');

        } catch (error) {
            this.showNotification('Erreur lors de l\'enregistrement', 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Enregistrer';
        }
    }

    editCategory(id) {
        const category = this.categories.find(c => c.id === id);
        if (!category) return;

        const form = document.getElementById('categoryForm');
        form.elements['id'].value = category.id;
        form.elements['name'].value = category.name;
        form.elements['description'].value = category.description;
        form.elements['icon'].value = category.icon;
        form.elements['color'].value = category.color;
        form.elements['active'].checked = category.active;

        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
    }

    async deleteCategory(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) return;

        try {
            // Simulation de suppression
            await new Promise(resolve => setTimeout(resolve, 500));
            
            this.categories = this.categories.filter(c => c.id !== id);
            this.renderCategories();
            this.updateStats();
            
            this.showNotification('Catégorie supprimée avec succès', 'success');
        } catch (error) {
            this.showNotification('Erreur lors de la suppression', 'danger');
        }
    }

    async toggleStatus(id, status) {
        try {
            // Simulation de mise à jour
            await new Promise(resolve => setTimeout(resolve, 300));
            
            const category = this.categories.find(c => c.id === id);
            if (category) {
                category.active = status;
                this.updateStats();
            }
            
            this.showNotification('Statut mis à jour avec succès', 'success');
        } catch (error) {
            this.showNotification('Erreur lors de la mise à jour du statut', 'danger');
        }
    }

    resetForm() {
        const form = document.getElementById('categoryForm');
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

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.categoryManager = new CategoryManager();
}); 