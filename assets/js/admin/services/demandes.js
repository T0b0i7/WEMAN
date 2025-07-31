import { store } from '../store.js';
import { notifications } from './notifications.js';

class DemandeService {
    constructor() {
        this.collection = 'demandes';
    }

    async getAll() {
        try {
            return await store.read(this.collection);
        } catch (error) {
            notifications.error('Erreur lors du chargement des demandes');
            throw error;
        }
    }

    async getById(id) {
        try {
            return await store.read(this.collection, id);
        } catch (error) {
            notifications.error('Erreur lors du chargement de la demande');
            throw error;
        }
    }

    async create(demandeData) {
        try {
            const demande = await store.create(this.collection, {
                ...demandeData,
                status: 'pending',
                date: new Date().toISOString()
            });
            notifications.success('Demande créée avec succès');
            return demande;
        } catch (error) {
            notifications.error('Erreur lors de la création de la demande');
            throw error;
        }
    }

    async update(id, updates) {
        try {
            const demande = await store.update(this.collection, id, updates);
            notifications.success('Demande mise à jour avec succès');
            return demande;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour de la demande');
            throw error;
        }
    }

    async delete(id) {
        try {
            await store.delete(this.collection, id);
            notifications.success('Demande supprimée avec succès');
            return true;
        } catch (error) {
            notifications.error('Erreur lors de la suppression de la demande');
            throw error;
        }
    }

    async updateStatus(id, status, comment = '') {
        try {
            const updates = { 
                status,
                statusDate: new Date().toISOString()
            };
            
            if (comment) {
                updates.statusComment = comment;
            }

            const demande = await this.update(id, updates);
            
            const messages = {
                pending: 'Demande mise en attente',
                accepted: 'Demande acceptée',
                rejected: 'Demande refusée',
                'in-progress': 'Demande en cours de traitement',
                completed: 'Demande terminée'
            };
            
            notifications.success(messages[status] || 'Statut mis à jour');
            return demande;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour du statut');
            throw error;
        }
    }

    validateDemande(demandeData) {
        const errors = [];
        
        if (!demandeData.client?.trim()) {
            errors.push('Le nom du client est requis');
        }
        if (!demandeData.type?.trim()) {
            errors.push('Le type de document est requis');
        }
        if (!demandeData.deadline) {
            errors.push('La date limite est requise');
        }
        if (new Date(demandeData.deadline) < new Date()) {
            errors.push('La date limite ne peut pas être dans le passé');
        }
        
        return errors;
    }

    async getStats() {
        const demandes = await this.getAll();
        return {
            total: demandes.length,
            pending: demandes.filter(d => d.status === 'pending').length,
            inProgress: demandes.filter(d => d.status === 'in-progress').length,
            completed: demandes.filter(d => d.status === 'completed').length,
            rejected: demandes.filter(d => d.status === 'rejected').length
        };
    }
}

export const demandeService = new DemandeService(); 