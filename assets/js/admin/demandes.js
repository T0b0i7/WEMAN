class DemandeManager {
    constructor() {
        // Initialisation des propriétés
        this.demandes = [];
        this.currentDemande = null;
        this.table = null;
        
        // Initialisation des modales
        this.modals = {
            view: new bootstrap.Modal(document.getElementById('viewModal')),
            edit: new bootstrap.Modal(document.getElementById('editModal')),
            delete: new bootstrap.Modal(document.getElementById('deleteModal'))
        };

        // Démarrer l'initialisation
        this.init();
    }

    // Méthode d'initialisation
    async init() {
        try {
            await this.loadDemandes();
            this.setupDataTable();
            this.setupEventListeners();
            this.updateStats();
        } catch (error) {
            console.error('Erreur d\'initialisation:', error);
            this.showNotification('Erreur d\'initialisation de l\'application', 'danger');
        }
    }

    // Configuration de DataTable
    setupDataTable() {
        this.table = $('#demandesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    }

    // Chargement des données
    async loadDemandes() {
        try {
            const response = await fetch('get_demandes.php');
            if (!response.ok) throw new Error('Erreur réseau');
            this.demandes = await response.json();
        } catch (error) {
            console.error('Erreur chargement données:', error);
            throw error;
        }
    }

    // Configuration des écouteurs d'événements
    setupEventListeners() {
        // Boutons d'action
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                this.viewDemande(id);
            });
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                this.editDemande(id);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id;
                this.confirmDelete(id);
            });
        });

        // Boutons des modales
        document.getElementById('saveEdit')?.addEventListener('click', () => this.saveDemande());
        document.getElementById('confirmDelete')?.addEventListener('click', () => this.deleteDemande());
    }

    // Mise à jour de la table
    updateTable() {
        if (this.table) {
            this.table.clear();
            this.table.rows.add(this.demandes);
            this.table.draw();
        }
    }

    // Mise à jour des statistiques
    updateStats() {
        document.getElementById('totalDemandes').textContent = this.demandes.length;
        document.getElementById('pendingDemandes').textContent = 
            this.demandes.filter(d => d.status === 'pending').length;
        document.getElementById('completedDemandes').textContent = 
            this.demandes.filter(d => d.status === 'completed').length;
        document.getElementById('rejectedDemandes').textContent = 
            this.demandes.filter(d => d.status === 'rejected').length;
    }

    // Mise à jour du statut
    async updateStatus(demandeId, newStatus) {
        try {
            // Simulation de mise à jour
            await new Promise(resolve => setTimeout(resolve, 500));
            
            const demande = this.demandes.find(d => d.id === parseInt(demandeId));
            if (demande) {
                demande.status = newStatus;
                this.updateTable();
                this.updateStats();
                
                const messages = {
                    accepted: 'Demande acceptée',
                    rejected: 'Demande refusée',
                    completed: 'Demande marquée comme terminée'
                };
                this.showNotification(messages[newStatus], 'success');
            }
        } catch (error) {
            this.showNotification('Erreur lors de la mise à jour', 'danger');
        }
    }

    // Affichage d'une demande
    viewDemande(id) {
        console.log('Viewing demande:', id);
        const demande = this.demandes.find(d => d.id === parseInt(id));
        if (!demande) {
            console.error('Demande non trouvée:', id);
            return;
        }

        // Animation de chargement
        const modal = $('#viewModal');
        modal.find('.modal-body').html('<div class="text-center"><div class="spinner-border"></div></div>');
        this.modals.view.show();

        // Simulation délai réseau
        setTimeout(() => {
            $('#view-client').text(demande.client);
            $('#view-type').text(demande.type);
            $('#view-date').text(new Date(demande.date).toLocaleDateString('fr-FR'));
            $('#view-deadline').text(new Date(demande.deadline).toLocaleDateString('fr-FR'));
            $('#view-status').html(`<span class="badge bg-${this.getStatusBadgeClass(demande.status)}">${this.getStatusLabel(demande.status)}</span>`);
            $('#view-budget').text(`${demande.budget} €`);
            $('#view-description').text(demande.description);

            // Animation d'apparition
            modal.find('.modal-body > *').hide().fadeIn(500);
        }, 500);
    }

    // Édition d'une demande
    editDemande(id) {
        console.log('Editing demande:', id);
        const demande = this.demandes.find(d => d.id === parseInt(id));
        if (!demande) {
            console.error('Demande non trouvée:', id);
            return;
        }

        this.currentDemande = demande;
        $('#edit-id').val(demande.id);
        $('#edit-status').val(demande.status);
        $('#edit-description').val(demande.description);

        this.modals.edit.show();
    }

    // Sauvegarde d'une demande
    async saveDemande() {
        const id = $('#edit-id').val();
        const status = $('#edit-status').val();
        const description = $('#edit-description').val();

        try {
            const response = await fetch('update_demande.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    status,
                    description
                })
            });

            if (!response.ok) throw new Error('Erreur réseau');

            // Mise à jour locale
            const demande = this.demandes.find(d => d.id === parseInt(id));
            if (demande) {
                demande.status = status;
                demande.description = description;
                this.updateTable();
                this.updateStats();
                this.modals.edit.hide();
                this.showNotification('Demande mise à jour avec succès', 'success');
            }
        } catch (error) {
            this.showNotification('Erreur lors de la mise à jour', 'danger');
        }
    }

    // Confirmation de suppression
    confirmDelete(id) {
        console.log('Confirming delete for demande:', id);
        this.currentDemande = this.demandes.find(d => d.id === parseInt(id));
        if (!this.currentDemande) {
            console.error('Demande non trouvée:', id);
            return;
        }
        this.modals.delete.show();
    }

    // Suppression d'une demande
    async deleteDemande() {
        if (!this.currentDemande) return;

        try {
            const response = await fetch('delete_demande.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: this.currentDemande.id
                })
            });

            if (!response.ok) throw new Error('Erreur réseau');

            // Animation de suppression
            $(`#demandesTable tr[data-id="${this.currentDemande.id}"]`)
                .fadeOut(400, () => {
                    this.demandes = this.demandes.filter(d => d.id !== this.currentDemande.id);
                    this.updateTable();
                    this.updateStats();
                });

            this.modals.delete.hide();
            this.showNotification('Demande supprimée avec succès', 'success');
        } catch (error) {
            this.showNotification('Erreur lors de la suppression', 'danger');
        }
    }

    // Récupération de la classe de badge de statut
    getStatusBadgeClass(status) {
        const classes = {
            en_attente: 'warning',
            en_cours: 'info',
            termine: 'success',
            annule: 'danger'
        };
        return classes[status] || 'secondary';
    }

    // Récupération de l'étiquette de statut
    getStatusLabel(status) {
        const labels = {
            en_attente: 'En attente',
            en_cours: 'En cours',
            termine: 'Terminé',
            annule: 'Annulé'
        };
        return labels[status] || status;
    }

    // Méthode de notification
    showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
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

// Initialisation avec log de débogage
$(document).ready(() => {
    console.log('Initializing DemandeManager...');
    window.demandeManager = new DemandeManager();
});