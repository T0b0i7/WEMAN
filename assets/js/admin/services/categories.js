import { store } from '../store.js';
import { notifications } from './notifications.js';

class CategoryService {
    constructor() {
        this.collection = 'categories';
    }

    async getAll() {
        try {
            return await store.read(this.collection);
        } catch (error) {
            notifications.error('Erreur lors du chargement des catégories');
            throw error;
        }
    }

    async getById(id) {
        try {
            return await store.read(this.collection, id);
        } catch (error) {
            notifications.error('Erreur lors du chargement de la catégorie');
            throw error;
        }
    }

    async create(categoryData) {
        try {
            const category = await store.create(this.collection, {
                ...categoryData,
                active: true
            });
            notifications.success('Catégorie créée avec succès');
            return category;
        } catch (error) {
            notifications.error('Erreur lors de la création de la catégorie');
            throw error;
        }
    }

    async update(id, updates) {
        try {
            const category = await store.update(this.collection, id, updates);
            notifications.success('Catégorie mise à jour avec succès');
            return category;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour de la catégorie');
            throw error;
        }
    }

    async delete(id) {
        try {
            // Vérifier si la catégorie contient des documents
            const documents = await store.read('documents');
            const hasDocuments = documents.some(doc => doc.categoryId === parseInt(id));
            
            if (hasDocuments) {
                notifications.warning('Impossible de supprimer une catégorie contenant des documents');
                return false;
            }

            await store.delete(this.collection, id);
            notifications.success('Catégorie supprimée avec succès');
            return true;
        } catch (error) {
            notifications.error('Erreur lors de la suppression de la catégorie');
            throw error;
        }
    }

    async toggleStatus(id) {
        try {
            const category = await this.getById(id);
            const updated = await this.update(id, { active: !category.active });
            notifications.success(
                updated.active ? 'Catégorie activée' : 'Catégorie désactivée'
            );
            return updated;
        } catch (error) {
            notifications.error('Erreur lors du changement de statut');
            throw error;
        }
    }

    validateCategory(categoryData) {
        const errors = [];
        
        if (!categoryData.name?.trim()) {
            errors.push('Le nom est requis');
        }
        if (!categoryData.icon?.trim()) {
            errors.push('L\'icône est requise');
        }
        if (!categoryData.color?.trim()) {
            errors.push('La couleur est requise');
        }
        
        return errors;
    }
}

export const categoryService = new CategoryService(); 