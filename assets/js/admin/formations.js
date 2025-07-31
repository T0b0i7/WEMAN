class FormationManager {
    constructor() {
        this.formations = [];
        this.modules = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadFormations();
    }

    setupEventListeners() {
        // Formulaire d'ajout/édition
        document.getElementById('formationForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveFormation();
        });

        // Ajout de module
        document.getElementById('addModule')?.addEventListener('click', () => {
            this.addModuleField();
        });
    }

    async loadFormations() {
        try {
            // Simulation de données pour le développement
            this.formations = [
                {
                    id: 1,
                    title: "Rédaction de Mémoire",
                    category: "academique",
                    price: 50000,
                    enrollments: 15,
                    status: "active",
                    modules: [
                        { title: "Introduction", duration: "2h" },
                        { title: "Méthodologie", duration: "4h" }
                    ]
                },
                {
                    id: 2,
                    title: "Excel Avancé",
                    category: "informatique",
                    price: 30000,
                    enrollments: 25,
                    status: "active",
                    modules: [
                        { title: "Formules", duration: "3h" },
                        { title: "Tableaux croisés", duration: "4h" }
                    ]
                }
            ];
            
            this.renderFormations();
            this.updateStats();
        } catch (error) {
            this.showNotification('Erreur lors du chargement des formations', 'danger');
        }
    }

    renderFormations() {
        const container = document.querySelector('#formationsList');
        if (!container) return;

        container.innerHTML = this.formations.map(formation => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-graduation-cap me-2"></i>
                        ${formation.title}
                    </div>
                </td>
                <td><span class="badge bg-light text-dark">${formation.category}</span></td>
                <td>${formation.price} FCFA</td>
                <td>${formation.enrollments}</td>
                <td>
                    <span class="badge bg-${formation.status === 'active' ? 'success' : 'warning'}">
                        ${formation.status === 'active' ? 'Active' : 'Brouillon'}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="formationManager.editFormation(${formation.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="formationManager.deleteFormation(${formation.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    updateStats() {
        const stats = {
            total: this.formations.length,
            active: this.formations.filter(f => f.status === 'active').length,
            enrollments: this.formations.reduce((sum, f) => sum + f.enrollments, 0),
            revenue: this.formations.reduce((sum, f) => sum + (f.price * f.enrollments), 0)
        };

        Object.entries(stats).forEach(([key, value]) => {
            const el = document.querySelector(`#${key}Count`);
            if (el) el.textContent = value;
        });
    }

    addModuleField() {
        const modulesList = document.getElementById('modulesList');
        const moduleId = Date.now();
        
        const moduleHtml = `
            <div class="module-item mb-3" data-id="${moduleId}">
                <div class="d-flex gap-3 align-items-start">
                    <div class="flex-grow-1">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="module_title[]" placeholder="Titre du module" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="module_duration[]" placeholder="Durée" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="formationManager.removeModule(${moduleId})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;

        modulesList.insertAdjacentHTML('beforeend', moduleHtml);
    }

    removeModule(moduleId) {
        const module = document.querySelector(`.module-item[data-id="${moduleId}"]`);
        module?.remove();
    }

    async saveFormation() {
        const form = document.getElementById('formationForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';

            // Simulation de sauvegarde
            await new Promise(resolve => setTimeout(resolve, 1000));

            this.showNotification('Formation enregistrée avec succès', 'success');
            bootstrap.Modal.getInstance(document.getElementById('formationModal')).hide();
            
            // Recharger les formations
            this.loadFormations();

        } catch (error) {
            this.showNotification('Erreur lors de l\'enregistrement', 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Enregistrer';
        }
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
    window.formationManager = new FormationManager();
}); 