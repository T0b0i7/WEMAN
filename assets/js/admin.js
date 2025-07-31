document.addEventListener('DOMContentLoaded', function() {
    // Toggle Sidebar
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    sidebarToggle.forEach(toggle => {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (window.innerWidth > 991.98) {
                mainContent.style.marginLeft = sidebar.classList.contains('active') ? '0' : '250px';
            }
        });
    });

    // Fermer le sidebar sur mobile lors du clic en dehors
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98 && 
            !e.target.closest('.sidebar') && 
            !e.target.closest('.sidebar-toggle')) {
            sidebar.classList.remove('active');
        }
    });

    // Gestion du login admin
    const loginForm = document.getElementById('adminLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = this.querySelector('[name="email"]').value;
            const password = this.querySelector('[name="password"]').value;
            const submitBtn = this.querySelector('[type="submit"]');
            const errorDiv = document.getElementById('loginError');

            try {
                // Désactiver le bouton et montrer le chargement
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connexion...';
                errorDiv.style.display = 'none';

                // Simuler une vérification d'authentification
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Vérification des identifiants (à remplacer par votre logique d'authentification)
                if (email === 'admin@wemantche.com' && password === 'admin123') {
                    // Stocker le token d'authentification
                    localStorage.setItem('adminToken', 'votre_token_jwt');
                    
                    // Rediriger vers le dashboard
                    window.location.href = 'dashboard.php';
                } else {
                    throw new Error('Identifiants incorrects');
                }
            } catch (error) {
                // Afficher l'erreur
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            } finally {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Se connecter';
            }
        });
    }

    // Vérification de l'authentification sur les pages admin
    function checkAdminAuth() {
        const adminToken = localStorage.getItem('adminToken');
        const isLoginPage = window.location.pathname.includes('login.php');

        if (!adminToken && !isLoginPage) {
            // Rediriger vers la page de login si non authentifié
            window.location.href = 'login.php';
        } else if (adminToken && isLoginPage) {
            // Rediriger vers le dashboard si déjà authentifié
            window.location.href = 'dashboard.php';
        }
    }

    // Vérifier l'authentification au chargement
    checkAdminAuth();

    // Gestion de la déconnexion
    const logoutBtn = document.querySelector('[data-action="logout"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.removeItem('adminToken');
            window.location.href = 'login.php';
        });
    }

    // Gestion responsive des tableaux
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const headerCells = table.querySelectorAll('th');
        const dataCells = table.querySelectorAll('td');

        // Ajouter des data-labels pour l'affichage mobile
        dataCells.forEach((cell, index) => {
            cell.setAttribute('data-label', headerCells[index % headerCells.length].textContent);
        });
    });
});

// Ajouter ces fonctions de sécurité
function isValidToken(token) {
    // Dans une vraie application, vérifier la validité du JWT
    return token && token.length > 0;
}

function checkPermissions(requiredRole) {
    const userRole = localStorage.getItem('adminRole');
    const roles = {
        'super_admin': 3,
        'admin': 2,
        'editor': 1
    };
    
    return roles[userRole] >= roles[requiredRole];
}

// Vérification de l'inactivité
let inactivityTimeout;
const INACTIVE_TIMEOUT = 30 * 60 * 1000; // 30 minutes

function resetInactivityTimer() {
    clearTimeout(inactivityTimeout);
    inactivityTimeout = setTimeout(secureLogout, INACTIVE_TIMEOUT);
}

// Écouter les événements d'activité
['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, resetInactivityTimer);
});

// Protection contre les attaques XSS
function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

// Gestion des erreurs
function handleError(error, context = '') {
    console.error(`Erreur ${context}:`, error);
    showNotification(`Une erreur est survenue ${context}`, 'danger');
    
    // Log l'erreur pour l'administrateur
    const errorLog = {
        timestamp: new Date().toISOString(),
        context,
        error: error.message,
        stack: error.stack,
        user: localStorage.getItem('adminEmail')
    };
    
    // Dans une vraie application, envoyer à un service de logging
    console.log('Error Log:', errorLog);
}

// Déterminer le rôle requis selon la page
function getRequiredRole(path) {
    const roleMap = {
        '/admin/settings.php': 'super_admin',
        '/admin/users.html': 'admin',
        '/admin/logs.html': 'admin'
    };
    
    return roleMap[path] || null;
}

// Amélioration de la fonction de déconnexion
function secureLogout() {
    try {
        // Annuler toutes les requêtes en cours
        if (window.activeRequests) {
            window.activeRequests.forEach(controller => controller.abort());
        }
        
        // Effacer les données sensibles
        const keysToKeep = ['theme', 'language']; // Préférences non sensibles
        const savedPrefs = {};
        
        keysToKeep.forEach(key => {
            savedPrefs[key] = localStorage.getItem(key);
        });
        
        // Nettoyer le stockage
        localStorage.clear();
        sessionStorage.clear();
        
        // Restaurer les préférences non sensibles
        Object.entries(savedPrefs).forEach(([key, value]) => {
            if (value) localStorage.setItem(key, value);
        });
        
        // Rediriger avec un message
        showNotification('Déconnexion réussie', 'info');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1000);
        
    } catch (error) {
        handleError(error, 'lors de la déconnexion');
        window.location.href = 'login.php'; // Forcer la redirection en cas d'erreur
    }
}

// Fonction utilitaire pour obtenir la classe du badge
function getBadgeClass(status) {
    const classes = {
        'actif': 'success',
        'inactif': 'danger',
        'en attente': 'warning'
    };
    return classes[status.toLowerCase()] || 'secondary';
}

// Gestion des filtres de tableau
function initTableFilters() {
    const filterInputs = document.querySelectorAll('.card-body .form-control, .card-body .form-select');
    
    filterInputs.forEach(input => {
        input.addEventListener('input', function() {
            filterTable(this);
        });
    });
}

function filterTable(input) {
    const table = document.querySelector('.table');
    const rows = table.querySelectorAll('tbody tr');
    const searchText = input.value.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
}

// Tri des colonnes
function initSortableColumns() {
    const headers = document.querySelectorAll('th[data-sortable]');
    
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.sortable;
            const direction = this.dataset.direction === 'asc' ? 'desc' : 'asc';
            
            // Reset autres colonnes
            headers.forEach(h => {
                h.dataset.direction = '';
                h.querySelector('i')?.remove();
            });
            
            // Mettre à jour l'icône de tri
            this.dataset.direction = direction;
            this.innerHTML += `<i class="fas fa-sort-${direction === 'asc' ? 'up' : 'down'} ms-1"></i>`;
            
            sortTable(column, direction);
        });
    });
}

function sortTable(column, direction) {
    const table = document.querySelector('.table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const tbody = table.querySelector('tbody');
    
    rows.sort((a, b) => {
        const aValue = a.querySelector(`td[data-field="${column}"]`).textContent.trim();
        const bValue = b.querySelector(`td[data-field="${column}"]`).textContent.trim();
        
        if (direction === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });
    
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

// Amélioration de la fonction de modification
function enhanceEditMode(row) {
    const cells = row.querySelectorAll('td[data-field]');
    
    cells.forEach(cell => {
        const field = cell.dataset.field;
        const value = cell.textContent.trim();
        
        switch(field) {
            case 'title':
            case 'description':
                const textarea = document.createElement('textarea');
                textarea.className = 'form-control form-control-sm';
                textarea.value = value;
                textarea.rows = field === 'description' ? 3 : 1;
                cell.innerHTML = '';
                cell.appendChild(textarea);
                break;
                
            case 'price':
                const priceGroup = document.createElement('div');
                priceGroup.className = 'input-group input-group-sm';
                priceGroup.innerHTML = `
                    <input type="number" class="form-control" value="${value.replace(/[^0-9]/g, '')}">
                    <span class="input-group-text">FCFA</span>
                `;
                cell.innerHTML = '';
                cell.appendChild(priceGroup);
                break;
                
            case 'image':
                const imageGroup = document.createElement('div');
                imageGroup.className = 'input-group input-group-sm';
                imageGroup.innerHTML = `
                    <input type="file" class="form-control" accept="image/*">
                    <img src="${value}" class="img-thumbnail" style="width: 40px; height: 40px;">
                `;
                cell.innerHTML = '';
                cell.appendChild(imageGroup);
                break;
                
            // ... autres cas spéciaux
        }
    });
}

// Amélioration de la fonction de sauvegarde
function enhanceSaveRow(row) {
    const inputs = row.querySelectorAll('input, select, textarea');
    const data = {};
    let hasError = false;
    
    inputs.forEach(input => {
        const cell = input.closest('td');
        const field = cell.dataset.field;
        
        // Validation selon le type
        if (input.required && !input.value) {
            input.classList.add('is-invalid');
            hasError = true;
            return;
        }
        
        if (field === 'email' && !isValidEmail(input.value)) {
            input.classList.add('is-invalid');
            hasError = true;
            return;
        }
        
        if (field === 'price' && (isNaN(input.value) || input.value < 0)) {
            input.classList.add('is-invalid');
            hasError = true;
            return;
        }
        
        data[field] = input.value;
    });
    
    if (hasError) {
        showNotification('Veuillez corriger les erreurs', 'danger');
        return;
    }
    
    // Animation de sauvegarde améliorée
    row.style.transition = 'all 0.3s ease';
    row.style.backgroundColor = '#e6ffe6';
    
    // Simuler un délai de sauvegarde
    showNotification('Sauvegarde en cours...', 'info');
    
    setTimeout(() => {
        updateRowDisplay(row, data);
        row.style.backgroundColor = '';
        showNotification('Modifications enregistrées avec succès', 'success');
    }, 800);
}

// Fonctions utilitaires
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function updateRowDisplay(row, data) {
    Object.entries(data).forEach(([field, value]) => {
        const cell = row.querySelector(`td[data-field="${field}"]`);
        if (cell) {
            cell.textContent = value;
        }
    });
}

// Vérification de la session admin
function checkAdminSession() {
    try {
        const token = localStorage.getItem('adminToken');
        const lastActivity = localStorage.getItem('lastActivity');
        const currentPath = window.location.pathname;
        
        // Vérifier l'expiration de la session
        if (lastActivity && (Date.now() - parseInt(lastActivity)) > INACTIVE_TIMEOUT) {
            throw new Error('Session expirée');
        }
        
        if (!isValidToken(token)) {
            if (currentPath.includes('/admin/') && !currentPath.includes('/admin/login.php')) {
                localStorage.setItem('redirectAfterLogin', currentPath);
                window.location.href = 'login.php';
            }
            return false;
        }
        
        // Mettre à jour le timestamp d'activité
        localStorage.setItem('lastActivity', Date.now().toString());
        
        // Vérifier les permissions
        const requiredRole = getRequiredRole(currentPath);
        if (requiredRole && !checkPermissions(requiredRole)) {
            showNotification('Accès non autorisé', 'danger');
            window.location.href = 'dashboard.php';
            return false;
        }
        
        return true;
    } catch (error) {
        handleError(error, 'lors de la vérification de session');
        secureLogout();
        return false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    checkAdminSession();
    
    // Toggle Sidebar
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    sidebarToggle.forEach(toggle => {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (window.innerWidth > 991.98) {
                mainContent.style.marginLeft = sidebar.classList.contains('active') ? '0' : '250px';
            }
        });
    });

    // Fermer le sidebar sur mobile lors du clic en dehors
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 991.98 && 
            !e.target.closest('.sidebar') && 
            !e.target.closest('.sidebar-toggle')) {
            sidebar.classList.remove('active');
        }
    });

    // Gestion du login admin
    const loginForm = document.getElementById('adminLoginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = this.querySelector('[name="email"]').value;
            const password = this.querySelector('[name="password"]').value;
            const submitBtn = this.querySelector('[type="submit"]');
            const errorDiv = document.getElementById('loginError');

            try {
                // Désactiver le bouton et montrer le chargement
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connexion...';
                errorDiv.style.display = 'none';

                // Simuler une vérification d'authentification
                await new Promise(resolve => setTimeout(resolve, 1000));

                // Vérification des identifiants (à remplacer par votre logique d'authentification)
                if (email === 'admin@wemantche.com' && password === 'admin123') {
                    // Stocker le token d'authentification
                    localStorage.setItem('adminToken', 'votre_token_jwt');
                    
                    // Rediriger vers le dashboard
                    window.location.href = 'dashboard.php';
                } else {
                    throw new Error('Identifiants incorrects');
                }
            } catch (error) {
                // Afficher l'erreur
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            } finally {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Se connecter';
            }
        });
    }

    // Vérification de l'authentification sur les pages admin
    function checkAdminAuth() {
        const adminToken = localStorage.getItem('adminToken');
        const isLoginPage = window.location.pathname.includes('login.php');

        if (!adminToken && !isLoginPage) {
            // Rediriger vers la page de login si non authentifié
            window.location.href = 'login.php';
        } else if (adminToken && isLoginPage) {
            // Rediriger vers le dashboard si déjà authentifié
            window.location.href = 'dashboard.php';
        }
    }

    // Vérifier l'authentification au chargement
    checkAdminAuth();

    // Gestion de la déconnexion
    const logoutBtn = document.querySelector('[data-action="logout"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.removeItem('adminToken');
            window.location.href = 'login.php';
        });
    }

    // Gestion responsive des tableaux
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const headerCells = table.querySelectorAll('th');
        const dataCells = table.querySelectorAll('td');

        // Ajouter des data-labels pour l'affichage mobile
        dataCells.forEach((cell, index) => {
            cell.setAttribute('data-label', headerCells[index % headerCells.length].textContent);
        });
    });

    // Gestion des tableaux
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        // Select All Checkbox
        const selectAll = table.querySelector('#selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = table.querySelectorAll('tbody .form-check-input');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        // Row Actions
        const actionButtons = table.querySelectorAll('.btn-group .btn');
        actionButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.getAttribute('title');
                const row = this.closest('tr');
                const id = row.querySelector('td:first-child').textContent;

                switch(action) {
                    case 'Modifier':
                    case 'Éditer':
                        // Logique de modification
                        console.log(`Modifier l'élément ${id}`);
                        break;
                    case 'Aperçu':
                    case 'Détails':
                        // Logique d'aperçu
                        console.log(`Voir les détails de ${id}`);
                        break;
                    case 'Supprimer':
                        if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                            console.log(`Supprimer l'élément ${id}`);
                        }
                        break;
                }
            });
        });
    });

    // Gestion des formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Animation du bouton
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Traitement...';
            submitBtn.disabled = true;

            // Simuler un traitement
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Succès!';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');

                // Réinitialiser le formulaire
                setTimeout(() => {
                    this.reset();
                    submitBtn.innerHTML = originalText;
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.disabled = false;

                    // Fermer la modal si présente
                    const modal = this.closest('.modal');
                    if (modal) {
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();
                    }
                }, 1500);
            }, 2000);
        });
    });

    // Gestion des filtres
    const filterForms = document.querySelectorAll('.card .row.g-3');
    filterForms.forEach(form => {
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                // Logique de filtrage
                console.log('Filtrage en cours...');
            });
        });
    });

    // Toggle Password Visibility
    const passwordToggles = document.querySelectorAll('.input-group .btn-outline-secondary');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // File Upload Preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = input.closest('.form-group').querySelector('.preview-image');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Notifications
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.innerHTML = message;
        
        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Export Data
    const exportButtons = document.querySelectorAll('.btn-outline-primary[title="Exporter"]');
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Logique d'export
            console.log('Export des données...');
            showNotification('Export réussi!');
        });
    });

    // Fonctions de modification et suppression pour toutes les tables
    function initTableActions() {
        // Modification
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const row = this.closest('tr');
                const id = row.dataset.id;
                const cells = row.querySelectorAll('td[data-field]');
                
                // Transformer les cellules en champs de formulaire
                cells.forEach(cell => {
                    const field = cell.dataset.field;
                    const value = cell.textContent.trim();
                    
                    // Si c'est un badge (pour le statut par exemple)
                    if (cell.querySelector('.badge')) {
                        const badgeText = cell.querySelector('.badge').textContent.trim();
                        const input = createInputForField(field, badgeText);
                        cell.innerHTML = '';
                        cell.appendChild(input);
                    } else {
                        const input = createInputForField(field, value);
                        cell.innerHTML = '';
                        cell.appendChild(input);
                    }
                });

                // Changer les boutons d'action
                const actionCell = row.querySelector('.btn-group');
                actionCell.innerHTML = `
                    <button class="btn btn-sm btn-success btn-save" title="Enregistrer">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-cancel" title="Annuler">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                // Ajouter les événements pour sauvegarder/annuler
                actionCell.querySelector('.btn-save').addEventListener('click', () => saveRow(row));
                actionCell.querySelector('.btn-cancel').addEventListener('click', () => cancelEdit(row));
            });
        });

        // Suppression
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const row = this.closest('tr');
                const id = row.dataset.id;
                const itemName = row.querySelector('td[data-field="name"]')?.textContent || 'cet élément';

                if (confirm(`Êtes-vous sûr de vouloir supprimer ${itemName} ?`)) {
                    // Animation de suppression
                    row.style.backgroundColor = '#ffe6e6';
                    row.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(100%)';
                        
                        setTimeout(() => {
                            row.remove();
                            showNotification(`${itemName} a été supprimé avec succès`, 'success');
                        }, 300);
                    }, 100);

                    // Ici, vous ajouteriez l'appel API pour la suppression réelle
                    console.log(`Deleting item with ID: ${id}`);
                }
            });
        });
    }

    // Fonction pour créer le bon type d'input selon le champ
    function createInputForField(field, value) {
        let input;
        
        switch(field) {
            case 'status':
                input = document.createElement('select');
                input.className = 'form-select form-select-sm';
                ['Actif', 'Inactif', 'En attente'].forEach(status => {
                    const option = document.createElement('option');
                    option.value = status.toLowerCase();
                    option.textContent = status;
                    option.selected = status.toLowerCase() === value.toLowerCase();
                    input.appendChild(option);
                });
                break;
                
            case 'password':
                input = document.createElement('div');
                input.className = 'input-group input-group-sm';
                input.innerHTML = `
                    <input type="password" class="form-control form-control-sm" value="${value}">
                    <button class="btn btn-outline-secondary btn-sm toggle-password" type="button">
                        <i class="fas fa-eye"></i>
                    </button>
                `;
                // Ajouter l'événement pour afficher/masquer le mot de passe
                const toggleBtn = input.querySelector('.toggle-password');
                const passwordInput = input.querySelector('input');
                toggleBtn.addEventListener('click', () => {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;
                    toggleBtn.querySelector('i').classList.toggle('fa-eye');
                    toggleBtn.querySelector('i').classList.toggle('fa-eye-slash');
                });
                return input;
                
            case 'date':
                input = document.createElement('input');
                input.type = 'date';
                input.className = 'form-control form-control-sm';
                input.value = value;
                break;
                
            case 'email':
                input = document.createElement('input');
                input.type = 'email';
                input.className = 'form-control form-control-sm';
                input.value = value;
                break;
                
            case 'role':
                input = document.createElement('select');
                input.className = 'form-select form-select-sm';
                ['Admin', 'Éditeur', 'Utilisateur'].forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.toLowerCase();
                    option.textContent = role;
                    option.selected = role.toLowerCase() === value.toLowerCase();
                    input.appendChild(option);
                });
                break;

            default:
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control form-control-sm';
                input.value = value;
        }
        
        input.dataset.originalValue = value;
        return input;
    }

    // Fonction pour sauvegarder les modifications
    function saveRow(row) {
        const inputs = row.querySelectorAll('input, select');
        const data = {};
        
        inputs.forEach(input => {
            const cell = input.closest('td');
            const field = cell.dataset.field;
            
            // Gérer les cas spéciaux (status, password, etc.)
            if (field === 'status') {
                const statusValue = input.value;
                cell.innerHTML = `<span class="badge bg-${getBadgeClass(statusValue)}">${statusValue}</span>`;
                data[field] = statusValue;
            } else if (field === 'password') {
                // Masquer le mot de passe dans l'affichage
                cell.textContent = '••••••••';
                data[field] = input.value;
            } else {
                cell.textContent = input.value;
                data[field] = input.value;
            }
        });

        // Animation de sauvegarde
        row.style.backgroundColor = '#e6ffe6';
        
        // Restaurer les boutons d'action
        restoreActionButtons(row);
        
        // Notification de succès
        showNotification('Modifications enregistrées avec succès', 'success');
        
        // Reset style
        setTimeout(() => {
            row.style.backgroundColor = '';
        }, 1000);

        console.log('Saving data:', data);
    }

    // Fonction pour annuler les modifications
    function cancelEdit(row) {
        const inputs = row.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            const cell = input.closest('td');
            cell.textContent = input.dataset.originalValue;
        });

        restoreActionButtons(row);
    }

    // Fonction pour restaurer les boutons d'action
    function restoreActionButtons(row) {
        const actionCell = row.querySelector('.btn-group');
        actionCell.innerHTML = `
            <button class="btn btn-sm btn-primary btn-edit" title="Modifier">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger btn-delete" title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        // Réinitialiser les événements
        initTableActions();
    }

    // Initialiser les actions au chargement de la page
    initTableActions();

    // Démarrer le timer d'inactivité
    resetInactivityTimer();

    // Gestion des filtres de tableau
    initTableFilters();
    initSortableColumns();

    // Gestion de la catégorie "Autres"
    initOthersCategory();

    // Gestion des documents
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.querySelector('input[placeholder="Rechercher un document..."]');
    
    if (categoryFilter && statusFilter && searchInput) {
        const filterDocuments = () => {
            const category = categoryFilter.value;
            const status = statusFilter.value;
            const search = searchInput.value.toLowerCase();
            
            document.querySelectorAll('tbody tr').forEach(row => {
                const rowCategory = row.querySelector('td:nth-child(2)').textContent;
                const rowStatus = row.querySelector('.badge').textContent;
                const rowTitle = row.querySelector('.fw-bold').textContent.toLowerCase();
                
                const matchCategory = !category || rowCategory === category;
                const matchStatus = !status || rowStatus.toLowerCase() === status;
                const matchSearch = !search || rowTitle.includes(search);
                
                row.style.display = matchCategory && matchStatus && matchSearch ? '' : 'none';
            });
        };

        categoryFilter.addEventListener('change', filterDocuments);
        statusFilter.addEventListener('change', filterDocuments);
        searchInput.addEventListener('input', filterDocuments);
    }

    // Gestion du modal d'ajout de document
    const addDocumentForm = document.getElementById('addDocumentForm');
    if (addDocumentForm) {
        addDocumentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simuler l'ajout d'un document
            const formData = new FormData(this);
            
            // Afficher un message de succès
            alert('Document publié avec succès !');
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addDocumentModal'));
            modal.hide();
            
            // Recharger la liste des documents
            // Dans une vraie application, on mettrait à jour la liste sans recharger
            location.reload();
        });
    }

    // Gestion des boutons d'action
    document.querySelectorAll('.btn-group .btn').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('title');
            const row = this.closest('tr');
            const documentTitle = row.querySelector('.fw-bold').textContent;

            switch(action) {
                case 'Modifier':
                    // Ouvrir le modal de modification avec les données du document
                    const editModal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
                    // Remplir le formulaire avec les données existantes
                    editModal.show();
                    break;

                case 'Aperçu':
                    // Ouvrir le modal d'aperçu
                    const previewModal = new bootstrap.Modal(document.getElementById('previewDocumentModal'));
                    // Charger l'aperçu du document
                    previewModal.show();
                    break;

                case 'Supprimer':
                    if (confirm(`Voulez-vous vraiment supprimer le document "${documentTitle}" ?`)) {
                        // Simuler la suppression
                        row.remove();
                        alert('Document supprimé avec succès !');
                    }
                    break;
            }
        });
    });

    // Gestion de la prévisualisation des images
    const imageInput = document.querySelector('input[type="file"][accept="image/*"]');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Afficher l'aperçu de l'image
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.style.maxWidth = '200px';
                    preview.style.marginTop = '10px';
                    
                    // Remplacer l'aperçu existant s'il y en a un
                    const existingPreview = imageInput.nextElementSibling;
                    if (existingPreview && existingPreview.tagName === 'IMG') {
                        existingPreview.remove();
                    }
                    imageInput.parentNode.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Ajouter cette section pour la gestion des formations
    initFormationsManagement();
});

// Ajouter ces fonctions de sécurité
function isValidToken(token) {
    // Dans une vraie application, vérifier la validité du JWT
    return token && token.length > 0;
}

function checkPermissions(requiredRole) {
    const userRole = localStorage.getItem('adminRole');
    const roles = {
        'super_admin': 3,
        'admin': 2,
        'editor': 1
    };
    
    return roles[userRole] >= roles[requiredRole];
}

// Vérification de l'inactivité
let inactivityTimeout;
const INACTIVE_TIMEOUT = 30 * 60 * 1000; // 30 minutes

function resetInactivityTimer() {
    clearTimeout(inactivityTimeout);
    inactivityTimeout = setTimeout(secureLogout, INACTIVE_TIMEOUT);
}

// Écouter les événements d'activité
['mousemove', 'keydown', 'click', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, resetInactivityTimer);
});

// Protection contre les attaques XSS
function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

// Gestion des erreurs
function handleError(error, context = '') {
    console.error(`Erreur ${context}:`, error);
    showNotification(`Une erreur est survenue ${context}`, 'danger');
    
    // Log l'erreur pour l'administrateur
    const errorLog = {
        timestamp: new Date().toISOString(),
        context,
        error: error.message,
        stack: error.stack,
        user: localStorage.getItem('adminEmail')
    };
    
    // Dans une vraie application, envoyer à un service de logging
    console.log('Error Log:', errorLog);
}

// Déterminer le rôle requis selon la page
function getRequiredRole(path) {
    const roleMap = {
        '/admin/settings.php': 'super_admin',
        '/admin/users.html': 'admin',
        '/admin/logs.html': 'admin'
    };
    
    return roleMap[path] || null;
}

// Amélioration de la fonction de déconnexion
function secureLogout() {
    try {
        // Annuler toutes les requêtes en cours
        if (window.activeRequests) {
            window.activeRequests.forEach(controller => controller.abort());
        }
        
        // Effacer les données sensibles
        const keysToKeep = ['theme', 'language']; // Préférences non sensibles
        const savedPrefs = {};
        
        keysToKeep.forEach(key => {
            savedPrefs[key] = localStorage.getItem(key);
        });
        
        // Nettoyer le stockage
        localStorage.clear();
        sessionStorage.clear();
        
        // Restaurer les préférences non sensibles
        Object.entries(savedPrefs).forEach(([key, value]) => {
            if (value) localStorage.setItem(key, value);
        });
        
        // Rediriger avec un message
        showNotification('Déconnexion réussie', 'info');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1000);
        
    } catch (error) {
        handleError(error, 'lors de la déconnexion');
        window.location.href = 'login.php'; // Forcer la redirection en cas d'erreur
    }
}

// Fonction utilitaire pour obtenir la classe du badge
function getBadgeClass(status) {
    const classes = {
        'actif': 'success',
        'inactif': 'danger',
        'en attente': 'warning'
    };
    return classes[status.toLowerCase()] || 'secondary';
}

// Gestion des filtres de tableau
function initTableFilters() {
    const filterInputs = document.querySelectorAll('.card-body .form-control, .card-body .form-select');
    
    filterInputs.forEach(input => {
        input.addEventListener('input', function() {
            filterTable(this);
        });
    });
}

function filterTable(input) {
    const table = document.querySelector('.table');
    const rows = table.querySelectorAll('tbody tr');
    const searchText = input.value.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
}

// Tri des colonnes
function initSortableColumns() {
    const headers = document.querySelectorAll('th[data-sortable]');
    
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.dataset.sortable;
            const direction = this.dataset.direction === 'asc' ? 'desc' : 'asc';
            
            // Reset autres colonnes
            headers.forEach(h => {
                h.dataset.direction = '';
                h.querySelector('i')?.remove();
            });
            
            // Mettre à jour l'icône de tri
            this.dataset.direction = direction;
            this.innerHTML += `<i class="fas fa-sort-${direction === 'asc' ? 'up' : 'down'} ms-1"></i>`;
            
            sortTable(column, direction);
        });
    });
}

function sortTable(column, direction) {
    const table = document.querySelector('.table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const tbody = table.querySelector('tbody');
    
    rows.sort((a, b) => {
        const aValue = a.querySelector(`td[data-field="${column}"]`).textContent.trim();
        const bValue = b.querySelector(`td[data-field="${column}"]`).textContent.trim();
        
        if (direction === 'asc') {
            return aValue.localeCompare(bValue);
        } else {
            return bValue.localeCompare(aValue);
        }
    });
    
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

// Amélioration de la fonction de modification
function enhanceEditMode(row) {
    const cells = row.querySelectorAll('td[data-field]');
    
    cells.forEach(cell => {
        const field = cell.dataset.field;
        const value = cell.textContent.trim();
        
        switch(field) {
            case 'title':
            case 'description':
                const textarea = document.createElement('textarea');
                textarea.className = 'form-control form-control-sm';
                textarea.value = value;
                textarea.rows = field === 'description' ? 3 : 1;
                cell.innerHTML = '';
                cell.appendChild(textarea);
                break;
                
            case 'price':
                const priceGroup = document.createElement('div');
                priceGroup.className = 'input-group input-group-sm';
                priceGroup.innerHTML = `
                    <input type="number" class="form-control" value="${value.replace(/[^0-9]/g, '')}">
                    <span class="input-group-text">FCFA</span>
                `;
                cell.innerHTML = '';
                cell.appendChild(priceGroup);
                break;
                
            case 'image':
                const imageGroup = document.createElement('div');
                imageGroup.className = 'input-group input-group-sm';
                imageGroup.innerHTML = `
                    <input type="file" class="form-control" accept="image/*">
                    <img src="${value}" class="img-thumbnail" style="width: 40px; height: 40px;">
                `;
                cell.innerHTML = '';
                cell.appendChild(imageGroup);
                break;
                
            // ... autres cas spéciaux
        }
    });
}

// Amélioration de la fonction de sauvegarde
function enhanceSaveRow(row) {
    const inputs = row.querySelectorAll('input, select, textarea');
    const data = {};
    let hasError = false;
    
    inputs.forEach(input => {
        const cell = input.closest('td');
        const field = cell.dataset.field;
        
        // Validation selon le type
        if (input.required && !input.value) {
            input.classList.add('is-invalid');
            hasError = true;
            return;
        }
        
        if (field === 'email' && !isValidEmail(input.value)) {
            input.classList.add('is-invalid');
            hasError = true;
            return;
        }
        
        if (field === 'price' && (isNaN(input.value) || input.value < 0)) {
            input.classList.add('is-invalid');
            hasError = true;
            return;
        }
        
        data[field] = input.value;
    });
    
    if (hasError) {
        showNotification('Veuillez corriger les erreurs', 'danger');
        return;
    }
    
    // Animation de sauvegarde améliorée
    row.style.transition = 'all 0.3s ease';
    row.style.backgroundColor = '#e6ffe6';
    
    // Simuler un délai de sauvegarde
    showNotification('Sauvegarde en cours...', 'info');
    
    setTimeout(() => {
        updateRowDisplay(row, data);
        row.style.backgroundColor = '';
        showNotification('Modifications enregistrées avec succès', 'success');
    }, 800);
}

// Fonctions utilitaires
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function updateRowDisplay(row, data) {
    Object.entries(data).forEach(([field, value]) => {
        const cell = row.querySelector(`td[data-field="${field}"]`);
        if (cell) {
            cell.textContent = value;
        }
    });
}