<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Catégories - WEMANTCHE Admin</title>
    <link rel="icon" type="image/jpg" href="../../assets/images/WEMANTCHE LOGO p 2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
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

        /* Sidebar - Nouveaux styles */
        .sidebar {
            width: 300px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border-color);
            background: rgba(67, 97, 238, 0.1);
        }

        .sidebar-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
        }

        .sidebar-header h5 {
            font-size: 1.5rem;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-link {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 15px;
          color: var(--text-white); /* Changé en blanc */
            transition: all 0.3s ease;
        }

        .nav-link i {
            font-size: 1.25rem;
            width: 30px;
            text-align: center;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-white);
            background: rgba(67, 97, 238, 0.1);
            border-left-color: var(--primary);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 300px;
            padding: 2rem;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        .card-header {
            background: var(--bg-card);
            padding: 1rem;
        }

        .card-header {
            background: rgba(67, 97, 238, 0.1);
            border-bottom: 1px solid var(--border-color);
        }

        /* Stats Cards Styling */
        .stats-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: linear-gradient(145deg, var(--bg-card), var(--bg-dark));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card:nth-child(1) {
            background: linear-gradient(145deg, #4361ee20, #4361ee40);
            border-left: 4px solid var(--primary);
        }

        .stats-card:nth-child(2) {
            background: linear-gradient(145deg, #2dd4bf20, #2dd4bf40);
            border-left: 4px solid var(--success);
        }

        .stats-card:nth-child(3) {
            background: linear-gradient(145deg, #3b82f620, #3b82f640);
            border-left: 4px solid var(--info);
        }

        .stats-card:nth-child(4) {
            background: linear-gradient(145deg, #f59e0b20, #f59e0b40);
            border-left: 4px solid var(--warning);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .stats-icon {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
            position: relative;
            z-index: 1;
        }

        .stats-icon::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 12px;
            opacity: 0.2;
            z-index: -1;
        }

        .stats-icon.bg-primary { 
            color: var(--primary);
            background: linear-gradient(145deg, #4361ee30, #4361ee10);
        }

        .stats-icon.bg-success { 
            color: var(--success);
            background: linear-gradient(145deg, #2dd4bf30, #2dd4bf10);
        }

        .stats-icon.bg-info { 
            color: var(--info);
            background: linear-gradient(145deg, #3b82f630, #3b82f610);
        }

        .stats-icon.bg-warning { 
            color: var(--warning);
            background: linear-gradient(145deg, #f59e0b30, #f59e0b10);
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-white);
            margin-bottom: 0.25rem;
            line-height: 1;
        }

        .stats-label {
          color: var(--text-white); /* Changé en blanc */
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.8;
        }

        /* En-tête styling */
        .h3 {
            color: var(--text-white);
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .btn-primary {
            background: linear-gradient(145deg, var(--primary), #2d3fff);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #2d3fff, var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        /* Tables */
        .table {
            color: var(--text-white);
        }

        .table th {
            background: rgba(67, 97, 238, 0.1);
          color: var(--text-white); /* Changé en blanc */
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
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
        @media (max-width: 1199.98px) {
            .sidebar {
                width: 280px;
            }
            .main-content {
                margin-left: 280px;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
            }
            
            .nav-link {
                padding: 1rem 1.5rem;
            }

            .sidebar-header {
                padding: 1.5rem;
            }
        }

        /* Ajout des styles pour les cartes de statistiques */
        .stats-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        

        /* Gradients pour les icônes */
        .stats-icon.bg-primary { 
            background: linear-gradient(135deg, var(--primary), #2d3fff);
            color: var(--text-white);
        }

        .stats-icon.bg-success { 
            background: linear-gradient(135deg, var(--success), #20a4f3);
            color: var(--text-white);
        }

        .stats-icon.bg-info { 
            background: linear-gradient(135deg, var(--info), #0ea5e9);
            color: var(--text-white);
        }

        .stats-icon.bg-warning { 
            background: linear-gradient(135deg, var(--warning), #ff8a00);
            color: var(--text-white);
        }

        /* Style des textes */
        .stats-value {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--text-white);
            margin-bottom: 0.5rem;
        }

        .stats-label {
          color: var(--text-white); /* Changé en blanc */
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Mise en page des cartes */
        .stats-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Styles pour le modal et les notifications */
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            background: rgba(67, 97, 238, 0.1);
            padding: 1rem 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
        }

        .modal-title {
            color: var(--text-white);
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Styles pour les formulaires dans le modal */
        .modal-body {
            padding: 1.5rem;
        }

        .form-label {
            color: var(--text-white);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control, 
        .form-select {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            transition: all 0.3s ease;
        }

        .form-control:focus, 
        .form-select:focus {
            background-color: var(--bg-dark);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            color: var(--text-white);
        }

        .form-control::placeholder {
          color: var(--text-white); /* Changé en blanc */
        }

        .form-select option {
            background-color: var(--bg-dark);
            color: var(--text-white);
        }

        .form-check-label {
            color: var(--text-white);
        }

        .form-check-input {
            background-color: var(--bg-dark);
            border-color: var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Boutons du modal */
        .btn-secondary {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
        }

        .btn-secondary:hover {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-white);
        }

        .modal .btn-primary {
            background: linear-gradient(145deg, var(--primary), #2d3fff);
            border: none;
            font-weight: 500;
        }

        .modal .btn-primary:hover {
            background: linear-gradient(145deg, #2d3fff, var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        /* Styles pour l'en-tête */
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            color: var(--text-white);
        }

        .page-header h1 {
            margin: 0;
            font-weight: 600;
            color: var(--text-white);
        }

        .page-header .btn-light {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--text-white);
            transition: all 0.3s ease;
        }

        .page-header .btn-light:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Style pour les titres et placeholders */
        h1, h2, h3, h4, h5, h6,
        .card-title,
        .modal-title {
            color: var(--text-white) !important;
        }

        /* Style pour les placeholders */
        ::-webkit-input-placeholder {
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        ::-moz-placeholder {
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        :-ms-input-placeholder {
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        ::placeholder {
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        /* Style pour les inputs et selects */
        .form-control,
        .form-select {
            background-color: var(--bg-dark) !important;
            border-color: var(--border-color) !important;
            color: var(--text-white) !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25) !important;
        }

        /* Style pour les labels */
        .form-label {
            color: var(--text-white) !important;
            font-weight: 500;
        }

        /* Style pour les options du select */
        .form-select option {
            background-color: var(--bg-dark);
            color: var(--text-white);
        }

        /* Style pour les en-têtes de tableau */
        .table thead th {
            color: var(--text-white) !important;
            font-weight: 500;
        }

        .btn-group .btn {
            color: var(--text-white);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
        }

        .btn-group .btn.active {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-group .btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .table th {
            white-space: nowrap;
        }

        /* Style du bouton flottant */
.btn-floating {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    z-index: 1000;
    transition: all 0.3s ease;
}

.btn-floating:hover {
    transform: scale(1.1) rotate(90deg);
    box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
}

/* Animation de l'icône */
.btn-floating i {
    transition: all 0.3s ease;
}

.btn-floating:hover i {
    transform: rotate(-90deg);
}

/* Style pour la modal de visualisation des tarifs */
.view-pricing-modal .table {
    margin: 0;
}

.view-pricing-modal .table th {
    width: 40%;
    background: rgba(67, 97, 238, 0.1);
    color: var(--text-white);
}

.view-pricing-modal .table td {
    color: var(--text-white);
}

.view-pricing-modal .swal2-html-container {
    margin: 1em 0;
}

.view-pricing-modal .swal2-popup {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
}

.view-pricing-modal .swal2-title {
    color: var(--text-white);
}

/* Styles pour la pagination */
.pagination {
    margin: 0;
    gap: 0.25rem;
}

.pagination .page-link {
    background: var(--bg-dark);
    border-color: var(--border-color);
    color: var(--text-white);
    padding: 0.5rem 0.75rem;
    min-width: 38px;
    text-align: center;
}

.pagination .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--text-white);
}

.pagination .page-item.disabled .page-link {
    background: var(--bg-dark);
    border-color: var(--border-color);
  color: var(--text-white); /* Changé en blanc */
}

/* Style pour le sélecteur de lignes */
#rowsPerPage {
    width: auto;
    min-width: 80px;
    background-color: var(--bg-dark);
    border-color: var(--border-color);
    color: var(--text-white) !important;
}

#rowsPerPage option {
    background-color: var(--bg-dark);
    color: var(--text-white);
}

.d-flex.align-items-center span {
    color: var(--text-white);
}

.pagination-info {
    font-size: 0.875rem;
}

/* Modifier les styles de la pagination */
.text-muted {
    color: var(--text-white) !important;
}

.d-flex.justify-content-between .text-muted {
    color: var(--text-white) !important;
}

/* Style pour les informations de pagination */
.card-footer .text-muted span {
    color: var(--text-white) !important;
    font-weight: 500;
}

.pagination-info {
    color: var(--text-white) !important;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .pagination-info {
        text-align: center;
    }

    .card-header .row {
        gap: 0.75rem;
    }
    
    .col-md-auto {
        width: 100%;
    }
    
    .d-flex.align-items-center {
        justify-content: center;
    }
}

.input-group .input-group-text {
    background: var(--bg-dark);
    border-color: var(--border-color);
  color: var(--text-white); /* Changé en blanc */
}

.form-select {
    background-color: var(--bg-dark);
    border-color: var(--border-color);
    color: var(--text-white);
}

.form-select option {
    background-color: var(--bg-dark);
    color: var(--text-white);
}
</style>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center">
                    <img src="../../assets/images/WEMANTCHE LOGO p 2.png" alt="Logo" class="sidebar-logo me-3">
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
                <!-- En-tête avec les onglets -->
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3">Gestion des Catégories</h1>
                        <div class="btn-group">
                            <button class="btn btn-light active" id="categoriesTab">
                                <i class="fas fa-folder me-2"></i>Catégories
                            </button>
                            <button class="btn btn-light" id="pricingTab" onclick="window.location.href='tarification.php'">
                                <i class="fas fa-tags me-2"></i>Tarification
                            </button>
                            <button class="btn btn-light" id="niveauxTab" onclick="window.location.href='niveaux_etudes.php'">
                                <i class="fas fa-graduation-cap me-2"></i>Niveau d'études
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section Catégories -->
                <div id="categoriesSection">
                    <!-- ... votre contenu existant ... -->
                    <button class="btn btn-primary btn-floating" 
                            data-bs-toggle="modal" 
                            data-bs-target="#categoryModal"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="left" 
                            title="Nouvelle Catégorie">
                        <i class="fas fa-plus"></i>
                    </button>

                    <!-- Statistiques -->
                    <div class="stats-wrapper">
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-primary">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stats-label">Total Catégories</div>
                                    <div class="stats-value" id="totalCount">0</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stats-label">Catégories Actives</div>
                                    <div class="stats-value" id="activeCount">0</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-info">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stats-label">Catégories Disponibles</div>
                                    <div class="stats-value" id="availableCount">0</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stats-card">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon bg-warning">
                                    <i class="fas fa-folder-plus"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="stats-label">Catégories Publiées</div>
                                    <div class="stats-value" id="publishedCount">0</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des catégories -->
                    <div class="card">
                        <div class="card-header border-bottom">
                            <div class="row align-items-center g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" class="form-control" id="searchInput" placeholder="Rechercher une catégorie...">
                                    </div>
                                </div>
                                <div class="col-md-auto ms-auto">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-white">Afficher</span>
                                        <select class="form-select form-select-sm" id="rowsPerPage" style="width: auto;">
                                            <option value="5">5</option>
                                            <option value="10" selected>10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                        <span class="text-white">lignes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="categoriesList">
                                        <!-- Les catégories seront ajoutées ici dynamiquement -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Affichage de <span id="startIndex">0</span> à <span id="endIndex">0</span> sur <span id="totalItems">0</span> entrées
                                </div>
                                <nav aria-label="Navigation des pages">
                                    <ul class="pagination mb-0" id="pagination"></ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Tarification -->
                <div id="pricingSection" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Prix Standard</th>
                                            <th>Prix Urgent</th>
                                            <th>Seuil Mots</th>
                                            <th>Prix/Mot Supp.</th>
                                            <th>Délai Standard</th>
                                            <th>Délai Urgent</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pricingList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bouton flottant pour ajouter un tarif -->
                    <button class="btn btn-primary btn-floating" 
                            data-bs-toggle="modal" 
                            data-bs-target="#pricingModal"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="left" 
                            title="Nouveau Tarif">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <!-- Section Niveaux d'études -->
                <div id="niveauxSection" style="display: none;">
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Catégorie -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" name="id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Statut *</label>
                            <select class="form-select" name="statut" required>
                                <option value="">Sélectionner un statut</option>
                                <option value="disponible">Disponible</option>
                                <option value="publié">Publié</option>
                            </select>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="active" checked>
                            <label class="form-check-label">Catégorie active</label>
                        </div>

                        <small class="text-muted">* Champs obligatoires</small>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="categoryForm" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto" id="toastTitle"></strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Ajouter ces variables au début du script
let currentPage = 1;
let rowsPerPage = 10;
let totalPages = 0;
let allCategories = [];
let filteredCategories = [];
let searchTerm = '';

document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM de base
    const categoriesList = document.getElementById('categoriesList');
    const totalCount = document.getElementById('totalCount');
    const activeCount = document.getElementById('activeCount');
    const availableCount = document.getElementById('availableCount');
    const publishedCount = document.getElementById('publishedCount');
    const categoryForm = document.getElementById('categoryForm');
    const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

    // Initialisation des tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Fonction de notification
    function showNotification(type, title, message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: title,
            text: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: type === 'success' ? '#28a745' : 
                       type === 'error' ? '#dc3545' : 
                       type === 'warning' ? '#ffc107' : '#17a2b8',
            color: '#fff',
            iconColor: '#fff'
        });
    }

    // Modifier la fonction fetchCategories
    function fetchCategories() {
        fetch('get_categories.php')
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau');
                return response.json();
            })
            .then(data => {
                if (!Array.isArray(data)) throw new Error('Format de données invalide');
                
                allCategories = data;
                filterAndDisplayCategories();
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Impossible de charger les catégories'
                });
            });
    }

    // Ajouter l'écouteur pour la recherche
    document.getElementById('searchInput').addEventListener('input', function(e) {
        searchTerm = e.target.value.toLowerCase();
        currentPage = 1; // Retour à la première page lors d'une recherche
        filterAndDisplayCategories();
    });

    // Fonction pour filtrer et afficher les catégories
    function filterAndDisplayCategories() {
        filteredCategories = allCategories.filter(category => {
            return category.nom.toLowerCase().includes(searchTerm) ||
                   (category.description && category.description.toLowerCase().includes(searchTerm)) ||
                   category.statut.toLowerCase().includes(searchTerm);
        });
        displayCategories();
    }

    // Modifier la fonction displayCategories
    function displayCategories() {
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedData = filteredCategories.slice(start, end);
        
        categoriesList.innerHTML = '';
        let counts = {
            total: filteredCategories.length,
            active: 0,
            available: 0,
            published: 0
        };

        paginatedData.forEach(category => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${escapeHtml(category.nom)}</td>
                <td>${escapeHtml(category.description || '')}</td>
                <td>
                    <span class="badge ${category.statut === 'publié' ? 'bg-success' : 'bg-warning'}">
                        ${escapeHtml(category.statut)}
                    </span>
                </td>
                <td>
                    <div class="btn-group-action">
                        <button class="btn btn-sm btn-info" onclick="viewCategory(${category.id})" title="Voir">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editCategory(${category.id})" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.id})" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            categoriesList.appendChild(row);

            counts.total++;
            if (category.active == 1) counts.active++;
            if (category.statut === 'disponible') counts.available++;
            if (category.statut === 'publié') counts.published++;
        });

        // Mettre à jour les compteurs
        totalCount.textContent = counts.total;
        activeCount.textContent = counts.active;
        availableCount.textContent = counts.available;
        publishedCount.textContent = counts.published;

        // Mettre à jour la pagination
        updateProfessionalPagination();
    }

    // Nouvelle fonction pour une pagination professionnelle
    function updateProfessionalPagination() {
        const totalItems = filteredCategories.length;
        totalPages = Math.ceil(totalItems / rowsPerPage);
        
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';
        
        // Si pas de résultats, ne pas afficher la pagination
        if (totalPages === 0) return;

        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        // Ajuster startPage si on est près de la fin
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // Première page
        pagination.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(1)" aria-label="Première page">
                    <i class="fas fa-angle-double-left"></i>
                </a>
            </li>
        `;

        // Page précédente
        pagination.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Précédent">
                    <i class="fas fa-angle-left"></i>
                </a>
            </li>
        `;

        // Afficher les points de suspension au début si nécessaire
        if (startPage > 1) {
            pagination.innerHTML += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
        }

        // Pages numérotées
        for (let i = startPage; i <= endPage; i++) {
            pagination.innerHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>
            `;
        }

        // Afficher les points de suspension à la fin si nécessaire
        if (endPage < totalPages) {
            pagination.innerHTML += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
        }

        // Page suivante
        pagination.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Suivant">
                    <i class="fas fa-angle-right"></i>
                </a>
            </li>
        `;

        // Dernière page
        pagination.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${totalPages})" aria-label="Dernière page">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            </li>
        `;
    }

    // Ajouter la fonction changePage
    window.changePage = function(page) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        displayCategories();
    }

    // Ajouter l'écouteur pour le changement de nombre de lignes
    document.getElementById('rowsPerPage').addEventListener('change', function(e) {
        rowsPerPage = parseInt(e.target.value);
        currentPage = 1;
        filterAndDisplayCategories();
    });

    // Échappement HTML pour la sécurité
    function escapeHtml(unsafe) {
        return unsafe?.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;") || '';
    }

    // Fonction de suppression
    window.deleteCategory = function(id) {
        if (!id) return;

        Swal.fire({
            title: 'Confirmer la suppression',
            text: 'Êtes-vous sûr de vouloir supprimer cette catégorie ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('delete_category.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erreur réseau');
                    return response.json();
                })
                .then(data => {
                    if (!data.success) throw new Error(data.message);
                    fetchCategories();
                    showNotification('success', 'Succès', 'Catégorie supprimée avec succès');
                })
                .catch(error => {
                    showNotification('error', 'Erreur', error.message);
                });
            }
        });
    };

    // Fonction de modification
    window.editCategory = function(id) {
        if (!id) return;

        fetch(`get_category.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Erreur lors de la récupération');
                }

                const form = document.getElementById('categoryForm');
                const modalTitle = document.querySelector('#categoryModal .modal-title');
                
                // Remplir le formulaire
                form.elements['id'].value = id;
                form.elements['name'].value = data.data.nom;
                form.elements['description'].value = data.data.description || '';
                form.elements['statut'].value = data.data.statut;
                form.elements['active'].checked = data.data.active == 1;

                // Mettre à jour le titre
                modalTitle.textContent = 'Modifier la catégorie';

                // Afficher le modal
                const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                modal.show();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Impossible de charger la catégorie',
                    confirmButtonText: 'OK'
                });
            });
    };

    // Fonction de visualisation
    window.viewCategory = function(id) {
        if (!id) return;

        fetch(`get_category.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);
                
                Swal.fire({
                    title: 'Détails de la catégorie',
                    html: `
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th>Nom</th>
                                    <td>${escapeHtml(data.data.nom)}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>${escapeHtml(data.data.description || '-')}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <span class="badge ${data.data.statut === 'publié' ? 'bg-success' : 'bg-warning'}">
                                            ${escapeHtml(data.data.statut)}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>État</th>
                                    <td>
                                        <span class="badge ${data.data.active == 1 ? 'bg-success' : 'bg-danger'}">
                                            ${data.data.active == 1 ? 'Actif' : 'Inactif'}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    `,
                    width: '600px',
                    confirmButtonText: 'Fermer'
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message || 'Impossible de charger les détails',
                    confirmButtonText: 'OK'
                });
            });
    };

    // Modifiez l'écouteur du formulaire pour gérer à la fois l'ajout et la modification
    categoryForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            id: this.elements['id'].value,
            name: this.elements['name'].value.trim(),
            description: this.elements['description'].value.trim(),
            statut: this.elements['statut'].value,
            active: this.elements['active'].checked ? 1 : 0
        };

        const url = formData.id ? 'update_category.php' : 'add_category.php';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message);
            }
            
            // Fermer le modal et réinitialiser le formulaire
            const modal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
            modal.hide();
            this.reset();
            
            // Recharger les catégories
            fetchCategories();
            
            // Afficher le message de succès
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.message,
                confirmButtonText: 'OK'
            });
        });
    });

    // Ajoutez un écouteur pour réinitialiser le formulaire quand la modal est fermée
    document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function () {
        const form = document.getElementById('categoryForm');
        form.reset();
        form.elements['id'].value = '';
        document.querySelector('#categoryModal .modal-title').textContent = 'Nouvelle catégorie';
    });

    // Chargement initial
    fetchCategories();
});
</script>
</body>
</html>