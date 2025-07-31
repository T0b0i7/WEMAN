import { store } from '../store.js';
import { notifications } from './notifications.js';

class UserService {
    constructor() {
        this.collection = 'users';
    }

    async getAll() {
        try {
            return await store.read(this.collection);
        } catch (error) {
            notifications.error('Erreur lors du chargement des utilisateurs');
            throw error;
        }
    }

    async getById(id) {
        try {
            return await store.read(this.collection, id);
        } catch (error) {
            notifications.error('Erreur lors du chargement de l\'utilisateur');
            throw error;
        }
    }

    async create(userData) {
        try {
            const user = await store.create(this.collection, {
                ...userData,
                status: 'active',
                registrationDate: new Date().toISOString()
            });
            notifications.success('Utilisateur créé avec succès');
            return user;
        } catch (error) {
            notifications.error('Erreur lors de la création de l\'utilisateur');
            throw error;
        }
    }

    async update(id, updates) {
        try {
            const user = await store.update(this.collection, id, updates);
            notifications.success('Utilisateur mis à jour avec succès');
            return user;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour de l\'utilisateur');
            throw error;
        }
    }

    async delete(id) {
        try {
            await store.delete(this.collection, id);
            notifications.success('Utilisateur supprimé avec succès');
            return true;
        } catch (error) {
            notifications.error('Erreur lors de la suppression de l\'utilisateur');
            throw error;
        }
    }

    async updateStatus(id, status) {
        try {
            const user = await this.update(id, { status });
            const messages = {
                active: 'Utilisateur activé',
                blocked: 'Utilisateur bloqué',
                pending: 'Utilisateur mis en attente'
            };
            notifications.success(messages[status] || 'Statut mis à jour');
            return user;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour du statut');
            throw error;
        }
    }

    async updateRole(id, role) {
        try {
            const user = await this.update(id, { role });
            notifications.success(`Rôle mis à jour : ${role}`);
            return user;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour du rôle');
            throw error;
        }
    }

    validateUser(userData) {
        const errors = [];
        
        if (!userData.firstName?.trim()) {
            errors.push('Le prénom est requis');
        }
        if (!userData.lastName?.trim()) {
            errors.push('Le nom est requis');
        }
        if (!userData.email?.trim()) {
            errors.push('L\'email est requis');
        } else if (!this.isValidEmail(userData.email)) {
            errors.push('L\'email n\'est pas valide');
        }
        if (userData.phone && !this.isValidPhone(userData.phone)) {
            errors.push('Le numéro de téléphone n\'est pas valide');
        }
        
        return errors;
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidPhone(phone) {
        return /^\+?[\d\s-]{8,}$/.test(phone);
    }
}

export const userService = new UserService(); 