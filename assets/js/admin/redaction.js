class RedactionManager {
    constructor() {
        this.table = null;
        this.currentDemande = null;
        this.init();
    }

    async init() {
        this.initDataTable();
        this.setupEventListeners();
        await this.loadDemandes();
    }

    initDataTable() {
        this.table = $('#demandesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
            },
            columns: [
                { data: 'id' },
                { data: 'date' },
                { data: 'client' },
                { data: 'type' },
                { data: 'level' },
                { data: 'pages' },
                { data: 'deadline' },
                { 
                    data: 'status',
                    render: (data) => this.renderStatus(data)
                },
                {
                    data: null,
                    render: (data) => this.renderActions(data)
                }
            ],
            order: [[1, 'desc']]
        });
    }

    setupEventListeners() {
        // Filtres rapides
        document.querySelectorAll('[data-filter]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const filter = e.target.dataset.filter;
                this.filterDemandes(filter);
            });
        });

        // Modal de détails
        const modal = document.getElementById('demandeModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', (e) => {
                const demandeId = e.relatedTarget.dataset.id;
                this.loadDemandeDetails(demandeId);
            });
        }

        // Sauvegarde des modifications
        document.getElementById('saveDemande')?.addEventListener('click', () => {
            this.saveDemande();
        });
    }

    async loadDemandes() {
        try {
            // Simuler chargement API
            const demandes = [
                {
                    id: 'DEM001',
                    date: '2024-03-15',
                    client: 'John Doe',
                    type: 'Mémoire',
                    level: 'Master',
                    pages: 50,
                    deadline: '2024-04-15',
                    status: 'pending'
                },
                // Autres demandes...
            ];

            this.table.clear().rows.add(demandes).draw();
        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors du chargement des demandes', 'danger');
        }
    }

    async loadDemandeDetails(id) {
        try {
            // Simuler chargement API
            const demande = {
                id: 'DEM001',
                // ... autres détails
            };

            this.currentDemande = demande;
            this.updateModalContent(demande);
        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors du chargement des détails', 'danger');
        }
    }

    async saveDemande() {
        const form = document.getElementById('traitementForm');
        const data = new FormData(form);
        const submitBtn = document.getElementById('saveDemande');

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';

            // Simuler appel API
            await new Promise(resolve => setTimeout(resolve, 1000));

            this.showNotification('Modifications enregistrées avec succès', 'success');
            this.loadDemandes(); // Recharger la table

            const modal = bootstrap.Modal.getInstance(document.getElementById('demandeModal'));
            modal?.hide();

        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors de la sauvegarde', 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Enregistrer les modifications';
        }
    }

    // Fonctions utilitaires
    renderStatus(status) {
        const statusClasses = {
            pending: 'warning',
            progress: 'info',
            completed: 'success',
            cancelled: 'danger'
        };

        return `<span class="badge bg-${statusClasses[status]}">${this.getStatusLabel(status)}</span>`;
    }

    renderActions(data) {
        return `
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#demandeModal" data-id="${data.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn btn-success" onclick="redactionManager.sendMessage('${data.id}')">
                    <i class="fas fa-envelope"></i>
                </button>
            </div>
        `;
    }

    getStatusLabel(status) {
        const labels = {
            pending: 'En attente',
            progress: 'En cours',
            completed: 'Terminée',
            cancelled: 'Annulée'
        };
        return labels[status] || status;
    }

    showNotification(message, type) {
        // Implémenter l'affichage des notifications
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.redactionManager = new RedactionManager();
}); 