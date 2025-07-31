// Store central pour gérer les données
class Store {
    constructor() {
        this.data = {
            users: [],
            categories: [],
            documents: [],
            formations: [],
            demandes: [],
            stats: {}
        };
        
        // Charger les données initiales depuis le localStorage
        this.loadFromStorage();
    }

    // Méthodes génériques CRUD
    async create(collection, item) {
        try {
            item.id = this.generateId(collection);
            item.createdAt = new Date().toISOString();
            this.data[collection].push(item);
            this.saveToStorage();
            return item;
        } catch (error) {
            console.error(`Erreur lors de la création dans ${collection}:`, error);
            throw error;
        }
    }

    async read(collection, id = null) {
        try {
            if (id) {
                return this.data[collection].find(item => item.id === parseInt(id));
            }
            return this.data[collection];
        } catch (error) {
            console.error(`Erreur lors de la lecture de ${collection}:`, error);
            throw error;
        }
    }

    async update(collection, id, updates) {
        try {
            const index = this.data[collection].findIndex(item => item.id === parseInt(id));
            if (index !== -1) {
                this.data[collection][index] = {
                    ...this.data[collection][index],
                    ...updates,
                    updatedAt: new Date().toISOString()
                };
                this.saveToStorage();
                return this.data[collection][index];
            }
            throw new Error('Item non trouvé');
        } catch (error) {
            console.error(`Erreur lors de la mise à jour dans ${collection}:`, error);
            throw error;
        }
    }

    async delete(collection, id) {
        try {
            this.data[collection] = this.data[collection].filter(item => item.id !== parseInt(id));
            this.saveToStorage();
            return true;
        } catch (error) {
            console.error(`Erreur lors de la suppression dans ${collection}:`, error);
            throw error;
        }
    }

    // Méthodes utilitaires
    generateId(collection) {
        const items = this.data[collection];
        return items.length ? Math.max(...items.map(item => item.id)) + 1 : 1;
    }

    loadFromStorage() {
        try {
            const stored = localStorage.getItem('wemantcheAdmin');
            if (stored) {
                this.data = JSON.parse(stored);
            } else {
                // Charger des données de démonstration
                this.loadDemoData();
            }
        } catch (error) {
            console.error('Erreur lors du chargement du storage:', error);
            this.loadDemoData();
        }
    }

    saveToStorage() {
        try {
            localStorage.setItem('wemantcheAdmin', JSON.stringify(this.data));
        } catch (error) {
            console.error('Erreur lors de la sauvegarde dans le storage:', error);
        }
    }

    loadDemoData() {
        // Données de démonstration
        this.data = {
            users: [
                {
                    id: 1,
                    firstName: "John",
                    lastName: "Doe",
                    email: "john@example.com",
                    phone: "+225 0123456789",
                    role: "admin",
                    status: "active",
                    createdAt: "2024-01-15T00:00:00.000Z"
                }
            ],
            categories: [
                {
                    id: 1,
                    name: "Mémoires",
                    description: "Mémoires universitaires",
                    icon: "fa-graduation-cap",
                    color: "#0066cc",
                    active: true,
                    createdAt: "2024-01-15T00:00:00.000Z"
                }
            ],
            documents: [
                {
                    id: 1,
                    title: "Guide de rédaction",
                    description: "Guide complet pour la rédaction académique",
                    categoryId: 1,
                    price: 5000,
                    downloads: 0,
                    rating: 0,
                    status: "published",
                    createdAt: "2024-01-15T00:00:00.000Z"
                }
            ],
            formations: [],
            demandes: [],
            stats: {
                totalUsers: 1,
                totalDocuments: 1,
                totalRevenue: 0,
                totalDownloads: 0
            }
        };
        this.saveToStorage();
    }

    // Méthodes spécifiques aux statistiques
    async updateStats() {
        const stats = {
            totalUsers: this.data.users.length,
            totalDocuments: this.data.documents.length,
            totalRevenue: this.data.documents.reduce((sum, doc) => sum + (doc.downloads * doc.price), 0),
            totalDownloads: this.data.documents.reduce((sum, doc) => sum + doc.downloads, 0)
        };
        this.data.stats = stats;
        this.saveToStorage();
        return stats;
    }
}

// Export de l'instance unique
export const store = new Store(); 