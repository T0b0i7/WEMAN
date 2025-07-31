import { store } from '../store.js';
import { notifications } from './notifications.js';

class DocumentService {
    constructor() {
        this.collection = 'documents';
    }

    async getAll() {
        try {
            return await store.read(this.collection);
        } catch (error) {
            notifications.error('Erreur lors du chargement des documents');
            throw error;
        }
    }

    async getById(id) {
        try {
            return await store.read(this.collection, id);
        } catch (error) {
            notifications.error('Erreur lors du chargement du document');
            throw error;
        }
    }

    async create(documentData) {
        try {
            // Ajout des propriétés par défaut
            const document = await store.create(this.collection, {
                ...documentData,
                downloads: 0,
                rating: 0,
                status: documentData.published ? 'published' : 'draft',
                views: 0,
                reviews: []
            });
            notifications.success('Document créé avec succès');
            return document;
        } catch (error) {
            notifications.error('Erreur lors de la création du document');
            throw error;
        }
    }

    async update(id, updates) {
        try {
            const document = await store.update(this.collection, id, updates);
            notifications.success('Document mis à jour avec succès');
            return document;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour du document');
            throw error;
        }
    }

    async delete(id) {
        try {
            await store.delete(this.collection, id);
            notifications.success('Document supprimé avec succès');
            return true;
        } catch (error) {
            notifications.error('Erreur lors de la suppression du document');
            throw error;
        }
    }

    async updateStatus(id, status) {
        try {
            const document = await this.update(id, { status });
            const messages = {
                published: 'Document publié',
                draft: 'Document sauvegardé en brouillon',
                archived: 'Document archivé'
            };
            notifications.success(messages[status] || 'Statut mis à jour');
            return document;
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour du statut');
            throw error;
        }
    }

    async addReview(id, review) {
        try {
            const document = await this.getById(id);
            const reviews = [...(document.reviews || []), review];
            
            // Calculer la nouvelle note moyenne
            const totalRating = reviews.reduce((sum, r) => sum + r.rating, 0);
            const rating = reviews.length ? totalRating / reviews.length : 0;

            await this.update(id, { reviews, rating });
            notifications.success('Avis ajouté avec succès');
            return document;
        } catch (error) {
            notifications.error('Erreur lors de l\'ajout de l\'avis');
            throw error;
        }
    }

    async incrementDownloads(id) {
        try {
            const document = await this.getById(id);
            return await this.update(id, { 
                downloads: (document.downloads || 0) + 1 
            });
        } catch (error) {
            notifications.error('Erreur lors de la mise à jour des téléchargements');
            throw error;
        }
    }

    validateDocument(documentData) {
        const errors = [];
        
        if (!documentData.title?.trim()) {
            errors.push('Le titre est requis');
        }
        if (!documentData.categoryId) {
            errors.push('La catégorie est requise');
        }
        if (!documentData.price && documentData.price !== 0) {
            errors.push('Le prix est requis');
        }
        if (documentData.price < 0) {
            errors.push('Le prix ne peut pas être négatif');
        }
        
        return errors;
    }

    // Méthodes utilitaires pour les filtres et la recherche
    async getByCategory(categoryId) {
        const documents = await this.getAll();
        return documents.filter(doc => doc.categoryId === parseInt(categoryId));
    }

    async search(query) {
        const documents = await this.getAll();
        const searchTerm = query.toLowerCase();
        
        return documents.filter(doc => 
            doc.title.toLowerCase().includes(searchTerm) ||
            doc.description?.toLowerCase().includes(searchTerm) ||
            doc.keywords?.some(kw => kw.toLowerCase().includes(searchTerm))
        );
    }

    async getStats() {
        const documents = await this.getAll();
        return {
            total: documents.length,
            published: documents.filter(d => d.status === 'published').length,
            downloads: documents.reduce((sum, d) => sum + (d.downloads || 0), 0),
            revenue: documents.reduce((sum, d) => sum + (d.downloads || 0) * d.price, 0),
            averageRating: documents.reduce((sum, d) => sum + (d.rating || 0), 0) / documents.length || 0
        };
    }
}

export const documentService = new DocumentService(); 