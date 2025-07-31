class SettingsManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadSettings();
    }

    setupEventListeners() {
        // Sauvegarde des paramètres
        document.getElementById('saveSettings')?.addEventListener('click', () => {
            this.saveSettings();
        });

        // Gestion des changements d'onglets
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', (e) => {
                if (e.target.dataset.bsTarget) {
                    this.handleTabChange(e.target.dataset.bsTarget.replace('#', ''));
                }
            });
        });
    }

    async loadSettings() {
        try {
            // Pour le développement, on utilise des données statiques
            const settings = {
                general: {
                    siteName: 'WEMANTCHE',
                    siteDescription: 'Plateforme de ressources académiques',
                    email: 'contact@wemantche.com',
                    phone: '+229 01 49 48 98 71'
                },
                payment: {
                    currency: 'XOF',
                    defaultPaymentMethod: 'momo',
                    minAmount: 1000,
                    maxAmount: 100000
                },
                notification: {
                    emailNotifications: true,
                    smsNotifications: false,
                    newsletterFrequency: 'weekly'
                }
            };

            this.populateSettings(settings);
        } catch (error) {
            console.error('Erreur lors du chargement des paramètres:', error);
            this.showNotification('Les paramètres par défaut ont été chargés', 'info');
        }
    }

    populateSettings(settings) {
        // Remplir les champs avec les données
        Object.entries(settings).forEach(([section, values]) => {
            Object.entries(values).forEach(([key, value]) => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type === 'checkbox') {
                        input.checked = value;
                    } else {
                        input.value = value;
                    }
                }
            });
        });
    }

    async saveSettings() {
        const submitBtn = document.getElementById('saveSettings');
        const originalText = submitBtn.innerHTML;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';

            const settings = this.getFormData();
            
            // Simuler une sauvegarde
            await new Promise(resolve => setTimeout(resolve, 1000));

            this.showNotification('Paramètres enregistrés avec succès', 'success');
            
        } catch (error) {
            console.error('Erreur lors de la sauvegarde:', error);
            this.showNotification('Erreur lors de la sauvegarde', 'danger');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    getFormData() {
        const settings = {};
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            const formData = new FormData(form);
            const formId = form.id.replace('SettingsForm', '').toLowerCase();
            settings[formId] = Object.fromEntries(formData);
        });
        
        return settings;
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
    window.settingsManager = new SettingsManager();
}); 