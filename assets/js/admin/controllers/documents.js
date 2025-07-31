import { documentService } from '../services/documents.js';
import { categoryService } from '../services/categories.js';
import { notifications } from '../services/notifications.js';

class DocumentManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFileManager();
    }

    setupEventListeners() {
        // Gestionnaire pour le formulaire de document
        const form = document.getElementById('documentForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Gestionnaire pour le bouton de bibliothèque
        const fileManagerBtn = document.querySelector('.btn-file-manager');
        if (fileManagerBtn) {
            fileManagerBtn.addEventListener('click', () => this.openFileManager());
        }

        // Gestionnaire pour l'upload direct
        const uploadInput = document.createElement('input');
        uploadInput.type = 'file';
        uploadInput.accept = '.pdf,.doc,.docx';
        uploadInput.style.display = 'none';
        document.body.appendChild(uploadInput);

        uploadInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFileSelect(e.target.files[0]);
            }
        });

        // Ajouter le gestionnaire de glisser-déposer
        const dropZone = document.getElementById('documentUpload');
        if (dropZone) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            dropZone.addEventListener('drop', (e) => {
                const file = e.dataTransfer.files[0];
                if (file) {
                    this.handleFileSelect(file);
                }
            });
        }
    }

    setupFileManager() {
        // Créer la modal du gestionnaire de fichiers
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'fileManagerModal';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sélectionner un document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="file" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                        <div class="file-list">
                            <!-- Les fichiers seront listés ici -->
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        this.fileManagerModal = new bootstrap.Modal(modal);

        // Gestionnaire pour l'upload dans la modal
        modal.querySelector('input[type="file"]').addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFileSelect(e.target.files[0]);
                this.fileManagerModal.hide();
            }
        });
    }

    handleFileSelect(file) {
        // Vérifier le type de fichier
        const allowedTypes = ['.pdf', '.doc', '.docx'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(fileExtension)) {
            this.showNotification('Type de fichier non autorisé', 'error');
            return;
        }

        // Vérifier la taille (50MB max)
        if (file.size > 50 * 1024 * 1024) {
            this.showNotification('Le fichier est trop volumineux (max 50MB)', 'error');
            return;
        }

        // Mettre à jour la prévisualisation
        this.updatePreview(file);

        // Stocker le fichier pour l'envoi
        document.getElementById('fileId').value = Date.now();
        this.selectedFile = file;

        this.showNotification('Document sélectionné avec succès', 'success');
    }

    updatePreview(file) {
        const preview = document.querySelector('.document-preview img');
        if (preview) {
            const fileType = file.name.split('.').pop().toLowerCase();
            preview.src = fileType === 'pdf' 
                ? '../../assets/images/pdf-preview.png'
                : '../../assets/images/doc-preview.png';
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        const form = e.target;

        try {
            if (!this.selectedFile) {
                this.showNotification('Veuillez sélectionner un document', 'error');
                return;
            }

            const formData = new FormData(form);
            formData.append('file', this.selectedFile);

            // Simuler l'envoi (à remplacer par votre API)
            this.showNotification('Document en cours d\'envoi...', 'info');
            await new Promise(resolve => setTimeout(resolve, 1500));
            
            this.showNotification('Document publié avec succès', 'success');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 1000);

        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors de la publication', 'error');
        }
    }

    openFileManager() {
        this.fileManagerModal.show();
    }

    showNotification(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.main-content');
        container.insertBefore(alertDiv, container.firstChild);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.documentManager = new DocumentManager();
}); 