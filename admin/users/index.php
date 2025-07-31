<?php
session_start();
require_once '../../config/connexion.php';
require_once '../../pages/protect-source.php';

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - WEMANTCHE Admin</title>
    <link rel="icon" type="image/jpg" href="../../assets/images/WEMANTCHE LOGO p 2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Variables et thème sombre */
        :root {
            --bg-dark: #1a1f2d;
            --bg-card: #242a38;
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #2dd4bf;
            --danger: #f43f5e;
            --warning: #f59e0b;
            --info: #3b82f6;
            --text-light: #94a3b8;
            --text-white: #ffffff;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        /* Style de base */
        body {
            background: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 300px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
        }

        .sidebar-header {
            padding: 1.8rem;
            border-bottom: 1px solid var(--border-color);
            background: rgba(67, 97, 238, 0.1);
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-header h5 {
            color: var(--text-white);
            font-weight: 600;
            font-size: 1.4rem;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-item {
            margin: 0.8rem 0;
        }

        .nav-link {
            padding: 1rem 1.8rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 15px;
          color: var(--text-white); /* Changé en blanc */
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .nav-link i {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-white);
            background: rgba(67, 97, 238, 0.1);
            border-left-color: var(--primary);
            transform: translateX(5px);
        }

        .nav-link:hover i,
        .nav-link.active i {
            color: var(--primary);
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 2rem;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .card-header {
            background: rgba(67, 97, 238, 0.1);
            border-bottom: 1px solid var(--border-color);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .stat-icon.bg-primary { background: linear-gradient(135deg, var(--primary), #2d3fff); }
        .stat-icon.bg-success { background: linear-gradient(135deg, var(--success), #20a4f3); }
        .stat-icon.bg-warning { background: linear-gradient(135deg, var(--warning), #ff8a00); }
        .stat-icon.bg-info { background: linear-gradient(135deg, var(--info), #0ea5e9); }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--text-white);
            margin-bottom: 0.5rem;
        }

        .stat-label {
          color: var(--text-white); /* Changé en blanc */
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Tables */
        .table {
            color: var(--text-white);
            border: none;
        }

        .table th,
        .table td {
            border: none;
            border-bottom: 1px solid var(--border-color);
        }

        /* Badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 500;
        }

        .badge.bg-success { background: var(--success) !important; }
        .badge.bg-warning { background: var(--warning) !important; }
        .badge.bg-info { background: var(--info) !important; }

        /* Charts */
        .chart-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: #2d3fff;
            transform: translateY(-2px);
        }

        /* Activity table */
        .activity-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .activity-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .row {
                margin: 0;
            }
        }

        /* Styles additionnels pour le nouveau design */
        .header-section {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stat-icon-bg {
            right: -10px;
            bottom: -10px;
            transform: rotate(-15deg);
        }

        .stat-content {
            z-index: 1;
        }

        .opacity-10 {
            opacity: 0.1;
        }

        .btn-light {
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }

        .badge {
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 30px;
        }

        .form-control, .form-select {
            text-align: justify !important;
        }
        
        .table td {
            text-align: justify !important;
            vertical-align: middle !important;
        }
        
        .modal-content {
            background-color: var(--bg-dark) !important;
        }

        /* Style pour le sélecteur d'entrées de DataTables */
        .dataTables_length {
            color: #ffffff !important;
            margin-bottom: 1rem;
        }

        .dataTables_length select {
            background-color: #2d3436 !important;
            color: #ffffff !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 6px;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem !important;
            margin: 0 0.5rem;
        }

        .dataTables_length select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }

        /* Style pour le texte "Afficher X entrées" */
        .dataTables_wrapper .dataTables_length label {
            color: #ffffff !important;
            font-weight: 500;
        }

        /* Style pour le texte de pagination et d'affichage des entrées */
        .dataTables_info {
            color: #ffffff !important;
            margin-top: 1rem;
        }

        /* Style pour la pagination */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #ffffff !important;
            background: transparent !important;
            border: 1px solid var(--border-color) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #ffffff !important;
        }

        /* Style pour le tableau sans bordures */
.table {
    color: var(--text-white);
    border: none;
}

.table th,
.table td {
    border: none;
    border-bottom: 1px solid var(--border-color);
}

/* Style pour la recherche DataTables */
.dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_filter label {
    color: var(--text-white) !important;
}

.dataTables_filter input {
    background-color: var(--bg-dark) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-white) !important;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    margin-left: 0.5rem;
}

.dataTables_filter input:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    outline: none;
}

.dataTables_filter input::placeholder {
    color: var(--text-white);
    opacity: 0.7;
}
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <!-- Copier la sidebar du dashboard ici -->
            <div class="sidebar-header">
                <div class="d-flex align-items-center">
                    <img src="../../assets/images/WEMANTCHE LOGO p 2.png" alt="Logo" class="sidebar-logo me-2">
                    <h5 class="mb-0">WEMANTCHE</h5>
                </div>
            </div>
            
    
            <ul class="sidebar-nav">
                <li class="nav-item active">
                    <a href="../dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../categories/index.php" class="nav-link">
                        <i class="fas fa-folder"></i>
                        <span>Catégories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../documents/index.php" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../redaction/demandes.php" class="nav-link">
                        <i class="fas fa-pen-fancy"></i>
                        <span>Demandes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../contacts/index.php" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../users/index.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        
        </nav>

        <!-- Contenu principal -->
        <main class="main-content">
            <div class="container-fluid py-4">
                <!-- En-tête avec effet de gradient -->
                <div class="header-section mb-5 p-4 rounded-3" style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 text-white mb-2">Gestion des Utilisateurs</h1>
                            <p class="text-light mb-0">Gérez vos utilisateurs en toute simplicité</p>
                        </div>
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#userModal">
                            <i class="fas fa-plus me-2"></i>Nouvel Utilisateur
                        </button>
                    </div>
                </div>

                <!-- Statistiques avec nouveau design -->
                <div class="stats-section mb-5">
                    <div class="row g-4">
                        <!-- Total Utilisateurs -->
                        <div class="col-md-4">
                            <div class="stat-card rounded-4 p-4 position-relative overflow-hidden" 
                                 style="background: linear-gradient(135deg, #4361ee, #3f37c9);">
                                <div class="stat-icon-bg position-absolute">
                                    <i class="fas fa-users fa-3x text-white opacity-10"></i>
                                </div>
                                <div class="stat-content position-relative">
                                    <h3 class="text-white mb-1" id="totalUsers">0</h3>
                                    <p class="text-light mb-0">Total Utilisateurs</p>
                                    <div class="stat-trend mt-3">
                                        <span class="badge bg-light text-primary">
                                            <i class="fas fa-chart-line me-1"></i>Total
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Utilisateurs Actifs -->
                        <div class="col-md-4">
                            <div class="stat-card rounded-4 p-4 position-relative overflow-hidden" 
                                 style="background: linear-gradient(135deg, #2dd4bf, #20a4f3);">
                                <div class="stat-icon-bg position-absolute">
                                    <i class="fas fa-user-check fa-3x text-white opacity-10"></i>
                                </div>
                                <div class="stat-content position-relative">
                                    <h3 class="text-white mb-1" id="activeUsers">0</h3>
                                    <p class="text-light mb-0">Utilisateurs Actifs</p>
                                    <div class="stat-trend mt-3">
                                        <span class="badge bg-light text-success">
                                            <i class="fas fa-check-circle me-1"></i>Actifs
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- En Attente -->
                        <div class="col-md-4">
                            <div class="stat-card rounded-4 p-4 position-relative overflow-hidden" 
                                 style="background: linear-gradient(135deg, #f59e0b, #ff8a00);">
                                <div class="stat-icon-bg position-absolute">
                                    <i class="fas fa-user-clock fa-3x text-white opacity-10"></i>
                                </div>
                                <div class="stat-content position-relative">
                                    <h3 class="text-white mb-1" id="pendingUsers">0</h3>
                                    <p class="text-light mb-0">En Attente</p>
                                    <div class="stat-trend mt-3">
                                        <span class="badge bg-light text-warning">
                                            <i class="fas fa-clock me-1"></i>En attente
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des utilisateurs -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Prénom</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th>Date de création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Les données seront chargées dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Utilisateur -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm" class="needs-validation" novalidate>
                        <input type="hidden" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" 
                                       name="prenom" required maxlength="50"
                                       pattern="[A-Za-zÀ-ÿ\s]{2,50}">
                                <div class="invalid-feedback">
                                    Le prénom doit contenir entre 2 et 50 caractères
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control bg-dark text-white border-secondary" 
                                       name="nom" required maxlength="50"
                                       pattern="[A-Za-zÀ-ÿ\s]{2,50}">
                                <div class="invalid-feedback">
                                    Le nom doit contenir entre 2 et 50 caractères
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark text-white border-secondary" 
                                   name="email" required maxlength="100">
                            <div class="invalid-feedback">
                                Veuillez entrer une adresse email valide
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control bg-dark text-white border-secondary" 
                                   name="telephone" required maxlength="20"
                                   pattern="[0-9+\s]{8,20}">
                            <div class="invalid-feedback">
                                Le numéro de téléphone doit contenir entre 8 et 20 caractères
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rôle</label>
                            <select class="form-select bg-dark text-white border-secondary text-justify" 
                                    name="role" 
                                    required 
                                    style="text-align-last: justify;">
                                <option value="utilisateur">Utilisateur</option>
                                <option value="administrateur">Administrateur</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select bg-dark text-white border-secondary text-justify" 
                                    name="statut" 
                                    required 
                                    style="text-align-last: justify;">
                                <option value="actif">Actif</option>
                                <option value="bloque">Bloqué</option>
                            </select>
                        </div>

                        <div class="mb-3 password-field">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" class="form-control bg-dark text-white border-secondary" 
                                   name="mot_de_passe" minlength="8">
                            <div class="invalid-feedback">
                                Le mot de passe doit contenir au moins 8 caractères
                            </div>
                            <small class="text-muted">Laissez vide pour ne pas modifier</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="userForm" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        class UserManager {
            constructor() {
                this.users = [];
                this.dataTable = null;
                this.loadUsers();
            }

            async loadUsers() {
                try {
                    const response = await fetch('get_users.php');
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.error || 'Une erreur est survenue');
                    }
                    
                    this.users = data.users;
                    this.renderUsers();
                    
                    // Mise à jour des statistiques
                    document.getElementById('totalUsers').textContent = data.total;
                    document.getElementById('activeUsers').textContent = data.actifs;
                    document.getElementById('pendingUsers').textContent = data.en_attente;
                    
                } catch (error) {
                    console.error('Erreur:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de charger les utilisateurs: ' + error.message
                    });
                }
            }

            renderUsers() {
                const tbody = document.querySelector('#usersTable tbody');
                tbody.innerHTML = '';

                this.users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td style="white-space: pre-line">${user.id}</td>
                        <td style="white-space: pre-line">${user.prenom}</td>
                        <td style="white-space: pre-line">${user.nom}</td>
                        <td style="white-space: pre-line; word-break: break-word">${user.email}</td>
                        <td style="white-space: pre-line">${user.telephone}</td>
                        <td style="white-space: pre-line">
                            ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                        </td>
                        <td style="white-space: pre-line">
                            <span class="badge ${this.getStatusBadgeClass(user.statut)}">
                                ${user.statut}
                            </span>
                        </td>
                        <td style="white-space: pre-line">
                            ${new Date(user.cree_a).toLocaleDateString('fr-FR')}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info me-1" onclick="viewUser(${user.id})" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${user.statut !== 'supprime' ? `
                                <button class="btn btn-sm btn-warning me-1" onclick="editUser(${user.id})" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="banUser(${user.id})" title="Supprimer définitivement">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                // Détruire l'instance existante de DataTable si elle existe
                if ($.fn.DataTable.isDataTable('#usersTable')) {
                    $('#usersTable').DataTable().destroy();
                }

                // Initialiser une nouvelle instance
                this.dataTable = $('#usersTable').DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                    },
                    order: [[0, 'desc']], // Trier par ID décroissant
                    pageLength: 10,
                    responsive: true
                });
            }

            getStatusBadgeClass(status) {
                switch(status) {
                    case 'actif': return 'bg-success';
                    case 'bloque': return 'bg-danger';
                    default: return 'bg-secondary';
                }
            }

            updateStats() {
                const totalUsers = this.users.length;
                const activeUsers = this.users.filter(user => user.statut === 'actif').length;
                const pendingUsers = this.users.filter(user => user.statut === 'en_attente').length;

                document.getElementById('totalUsers').textContent = totalUsers;
                document.getElementById('activeUsers').textContent = activeUsers;
                document.getElementById('pendingUsers').textContent = pendingUsers;
            }

            async saveUser(formData) {
                try {
                    const response = await fetch('edit_user.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            text: data.message
                        }).then(() => {
                            // Rafraîchir la page après la modification
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.error);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        text: error.message
                    });
                }
            }

            async deleteUser(id) {
                try {
                    const formData = new FormData();
                    formData.append('id', id);
                    
                    const response = await fetch('delete_user.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            text: data.message
                        }).then(() => {
                            // Rafraîchir la page après la suppression
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.error);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        text: error.message
                    });
                }
            }
        }

        // Fonction générique de rafraîchissement
        function refreshAfterAction(message = 'Opération réussie') {
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            window.userManager = new UserManager();

            // Gestion du formulaire
            document.getElementById('userForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                await window.userManager.saveUser(formData);
            });

            // Confirmation de suppression
            window.confirmDeleteUser = (id) => {
                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: 'Cette action est irréversible',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.userManager.deleteUser(id);
                    }
                });
            };
        });

        function confirmDeleteUser(id) {
            // Afficher la boîte de dialogue de confirmation
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous ne pourrez pas annuler cette action !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteUser(id);
                }
            });
        }

        async function deleteUser(id) {
            try {
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                formData.append('id', id);
                const response = await fetch(`delete_user.php`, {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error(`Erreur du serveur: ${response.status} - ${response.statusText}`);
                }
                const result = await response.json();
                if (result.success) {
                    Swal.fire(
                        'Supprimé !',
                        'L\'utilisateur a été supprimé.',
                        'success'
                    );
                    // Recharger les utilisateurs
                    window.userManager.loadUsers();
                } else {
                    Swal.fire(
                        'Erreur !',
                        'Une erreur est survenue lors de la suppression de l\'utilisateur.',
                        'error'
                    );
                }
            } catch (error) {
                console.error('Erreur lors de la suppression de l\'utilisateur:', error);
                Swal.fire(
                    'Erreur !',
                    'Une erreur est survenue lors de la suppression de l\'utilisateur.',
                    'error'
                );
            }
        }

        function viewUser(id) {
            fetch(`view_user.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.user) {
                        const user = data.user;
                        Swal.fire({
                            title: 'Détails de l\'utilisateur',
                            html: `
                                <p><strong>ID:</strong> ${user.id}</p>
                                <p><strong>Prénom:</strong> ${user.prenom}</p>
                                <p><strong>Nom:</strong> ${user.nom}</p>
                                <p><strong>Email:</strong> ${user.email}</p>
                                <p><strong>Téléphone:</strong> ${user.telephone}</p>
                                <p><strong>Rôle:</strong> ${user.role}</p>
                                <p><strong>Statut:</strong> ${user.statut}</p>
                                <p><strong>Date de création:</strong> ${new Date(user.cree_a).toLocaleDateString('fr-FR')}</p>
                                <p><strong>Date de mise à jour:</strong> ${new Date(user.mis_a_jour_a).toLocaleDateString('fr-FR')}</p>
                            `,
                            icon: 'info'
                        });
                    } else {
                        Swal.fire('Erreur', data.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Impossible de charger les détails de l\'utilisateur', 'error');
                });
        }

        function editUser(id) {
            fetch(`view_user.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.user) {
                        const user = data.user;
                        document.querySelector('#userForm [name="id"]').value = user.id;
                        document.querySelector('#userForm [name="prenom"]').value = user.prenom;
                        document.querySelector('#userForm [name="nom"]').value = user.nom;
                        document.querySelector('#userForm [name="email"]').value = user.email;
                        document.querySelector('#userForm [name="telephone"]').value = user.telephone;
                        document.querySelector('#userForm [name="role"]').value = user.role;
                        document.querySelector('#userForm [name="statut"]').value = user.statut;
                        
                        // Masquer le champ mot de passe pour la modification
                        document.querySelector('#userForm [name="mot_de_passe"]').parentElement.style.display = 'none';
                        
                        $('#userModal').modal('show');
                    } else {
                        Swal.fire('Erreur', data.error, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur', 'Impossible de charger les détails de l\'utilisateur', 'error');
                });
        }

        // Gestionnaire pour l'ajout d'utilisateur
        function handleAddUser(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('add_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    refreshAfterAction('Utilisateur ajouté avec succès');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.error || 'Une erreur est survenue'
                    });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire('Erreur', 'Impossible d\'ajouter l\'utilisateur', 'error');
            });
        }

        // Remplacer la fonction handleEditUser par celle-ci
        function handleEditUser(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Validation du formulaire
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            Swal.fire({
                title: 'Modification en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('edit_user.php', {
                method: 'POST',
                body: formData,
                cache: 'no-cache'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    refreshAfterAction('Utilisateur modifié avec succès');
                } else {
                    throw new Error(data.error || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Impossible de modifier l\'utilisateur'
                });
            });
        }

        // Initialisation des gestionnaires de formulaire
        document.querySelector('#userForm').addEventListener('submit', function(e) {
            const isEdit = this.querySelector('[name="id"]').value;
            if (isEdit) {
                handleEditUser.call(this, e);
            } else {
                handleAddUser.call(this, e);
            }
        });

        // Réinitialiser le formulaire et afficher le champ mot de passe lors de l'ouverture pour un nouvel utilisateur
        document.querySelector('[data-bs-toggle="modal"]').addEventListener('click', function() {
            document.querySelector('#userForm').reset();
            document.querySelector('#userForm [name="id"]').value = '';
            document.querySelector('#userForm [name="mot_de_passe"]').parentElement.style.display = 'block';
        });

        async function banUser(id) {
            try {
                const result = await Swal.fire({
                    title: 'Suppression définitive',
                    text: "Cette action est irréversible. Voulez-vous vraiment supprimer cet utilisateur ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler'
                });

                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);

                    const response = await fetch('delete_user.php', {
                        method: 'POST',
                        body: formData,
                        cache: 'no-cache' // Ajout pour éviter les problèmes de cache
                    });

                    if (!response.ok) {
                        throw new Error(`Erreur HTTP: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (data.success) {
                        refreshAfterAction('Utilisateur supprimé avec succès');
                    } else {
                        throw new Error(data.error || 'Erreur lors de la suppression');
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Une erreur est survenue lors de la suppression'
                });
            }
        }

        document.querySelector('#userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const isEdit = formData.get('id'); // Si id existe, c'est une modification
            
            const url = isEdit ? 'edit_user.php' : 'add_user.php';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: isEdit ? 'Utilisateur modifié avec succès' : 'Utilisateur ajouté avec succès',
                        timer: 1500
                    }).then(() => {
                        $('#userModal').modal('hide');
                        window.userManager.loadUsers();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: data.error || 'Une erreur est survenue'
                    });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire('Erreur', `Impossible de ${isEdit ? 'modifier' : 'ajouter'} l'utilisateur`, 'error');
            });
        });
    </script>
</body>
</html>