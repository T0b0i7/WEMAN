<?php
require_once '../../config/connexion.php';

// Récupération des données
try {
    // Récupération des classes
    $stmt_classes = $pdo->query("SELECT * FROM classes ORDER BY ordre ASC");
    $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupération des filières
    $stmt_filieres = $pdo->query("SELECT * FROM filieres ORDER BY departement, nom ASC");
    $filieres = $stmt_filieres->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des matières
    $stmt_matieres = $pdo->query("SELECT * FROM matieres ORDER BY nom ASC");
    $matieres = $stmt_matieres->fetchAll(PDO::FETCH_ASSOC);
    $total_matieres = count($matieres);

    // Statistiques
    $total_classes = count($classes);
    $total_filieres = count($filieres);
    $classes_actives = array_filter($classes, fn($c) => $c['actif'] == 1);
    $filieres_actives = array_filter($filieres, fn($f) => $f['actif'] == 1);
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Niveaux d'Études - WEMANTCHE Admin</title>
    <link rel="icon" type="image/jpg" href="../../assets/images/WEMANTCHE LOGO p 2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

/* Styles pour la sidebar et le layout */
.admin-wrapper {
    display: flex;
    min-height: 100vh;
}

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
    margin-bottom: 0;
}

.sidebar-nav {
    padding: 1.5rem 0;
}

.nav-item {
    margin-bottom: 0.5rem;
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
    border-left: 3px solid var(--primary);
}

/* Ajustement du contenu principal */
.container-fluid {
    margin-left: 300px;
    width: calc(100% - 300px);
}

/* Styles spécifiques pour les tableaux de classes et filières */
.table th {
    background: rgba(67, 97, 238, 0.1);
    border-bottom: 1px solid var(--border-color);
}

.table-dark {
    background: var(--bg-card);
    color: var(--text-white);
}

.table-dark td, 
.table-dark th {
    border-color: var(--border-color);
}

/* Ajoutez ceci dans la section style existante */
.table td {
    white-space: normal !important;
    word-wrap: break-word;
    max-width: 200px; /* Ajustez selon vos besoins */
}

#matieresTable td:first-child {
    max-width: 300px;
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.matiere-nom {
    display: block;
    line-height: 1.4;
    margin: 0;
    padding: 0;
}

/* Style pour les badges de niveau */
.badge.bg-info {
    background: linear-gradient(145deg, var(--info), #0ea5e9) !important;
}

.badge.bg-primary {
    background: linear-gradient(145deg, var(--primary), #2d3fff) !important;
}

/* Style pour les statistiques */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stats-card {
    background: var(--bg-card);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
}

/* Animation pour les boutons d'action */
.btn-sm {
    transition: all 0.3s ease;
}

.btn-sm:hover {
    transform: translateY(-2px);
}

/* Style pour les modales */
.modal-content {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
}

.modal-header,
.modal-footer {
    border-color: var(--border-color);
}

.form-control,
.form-select {
    background: var(--bg-dark);
    border: 1px solid var(--border-color);
    color: var(--text-white);
}

.form-control:focus,
.form-select:focus {
    background: var(--bg-dark);
    border-color: var(--primary);
    color: var(--text-white);
    box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
}

.sidebar-header h5 {
    color: var(--text-white);
    margin: 0;
    font-weight: 600;
}

.page-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    color: var(--text-white);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.page-header h1 {
    margin: 0;
    font-weight: 600;
    font-size: 1.75rem;
    color: var(--text-white) !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.page-header .btn-light {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: var(--text-white);
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.page-header .btn-light:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

.page-header .btn-light.active {
    background: rgba(255, 255, 255, 0.3);
    border-color: var(--text-white);
    font-weight: 600;
}

.page-header .btn-group {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Ajoutez ces styles dans la section <style> existante */
.dataTables_wrapper {
    padding: 1rem 0;
}

.dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_filter input {
    background-color: var(--bg-dark) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-white) !important;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    width: 250px !important;
}

.dataTables_length select {
    background-color: var(--bg-dark) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-white) !important;
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    border-radius: 0.25rem;
}

.dataTables_info,
.dataTables_paginate {
    margin-top: 1rem;
    color: var(--text-light) !important;
}

.paginate_button {
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border: 1px solid var(--border-color);
    border-radius: 0.25rem;
    color: var(--text-light) !important;
    cursor: pointer;
}

.paginate_button.current {
    background-color: var(--primary) !important;
    border-color: var(--primary);
    color: var(--text-white) !important;
}

.paginate_button:hover:not(.current) {
    background-color: var(--bg-card) !important;
    border-color: var(--primary);
    color: var(--text-white) !important;
}
</style>
</head>
<body class="bg-dark">
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
                <li class="nav-item">
                    <a href="../dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li> 
                <li class="nav-item active">
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
        <div class="container-fluid py-4">
            <!-- En-tête avec les onglets -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 text-white">Gestion des Niveaux d'Études</h1>
                    <div class="btn-group">
                        <a href="index.php" class="btn btn-light">
                            <i class="fas fa-folder me-2"></i>Catégories
                        </a>
                        <a href="index.php#pricing" class="btn btn-light">
                            <i class="fas fa-tags me-2"></i>Tarification
                        </a>
                        <a href="niveaux_etudes.php" class="btn btn-light active">
                            <i class="fas fa-graduation-cap me-2"></i>Niveau d'études
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5><i class="fas fa-graduation-cap me-2"></i>Classes</h5>
                            <h3><?= $total_classes ?></h3>
                            <small><?= count($classes_actives) ?> actives</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5><i class="fas fa-university me-2"></i>Filières</h5>
                            <h3><?= $total_filieres ?></h3>
                            <small><?= count($filieres_actives) ?> actives</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5><i class="fas fa-book me-2"></i>Matières</h5>
                            <h3><?= $total_matieres ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Classes -->
            <div class="card bg-dark text-white mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Classes</h3>
                    <button class="btn btn-primary btn-add" data-type="classe">
                        <i class="fas fa-plus me-2"></i>Ajouter
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover" id="classesTable">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Niveau</th>
                                    <th>Ordre</th>
                                    <th>État</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($classes as $classe): ?>
                                <tr>
                                    <td><?= htmlspecialchars($classe['nom']) ?></td>
                                    <td><span class="badge bg-info"><?= ucfirst($classe['niveau']) ?></span></td>
                                    <td><?= $classe['ordre'] ?></td>
                                    <td>
                                        <span class="badge <?= $classe['actif'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $classe['actif'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info me-2" onclick="viewClasse(<?= $classe['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning me-2" onclick="editClasse(<?= $classe['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteClasse(<?= $classe['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Section Filières -->
            <div class="card bg-dark text-white">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-university me-2"></i>Filières</h3>
                    <button class="btn btn-primary btn-add" data-type="filiere">
                        <i class="fas fa-plus me-2"></i>Ajouter
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="filieresTable" class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Département</th>
                                    <th>Niveau</th>
                                    <th>État</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($filieres as $filiere): ?>
                                <tr>
                                    <td><?= htmlspecialchars($filiere['nom']) ?></td>
                                    <td><?= htmlspecialchars($filiere['departement']) ?></td>
                                    <td><span class="badge bg-primary"><?= ucfirst($filiere['niveau']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $filiere['actif'] ? 'bg-success' : 'bg-danger' ?>">
                                            <?= $filiere['actif'] ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info me-2" onclick="viewFiliere(<?= $filiere['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning me-2" onclick="editFiliere(<?= $filiere['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteFiliere(<?= $filiere['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Section Matières -->
            <div class="card bg-dark text-white mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-book me-2"></i>Matières</h3>
                    <button class="btn btn-primary btn-add" data-type="matiere">
                        <i class="fas fa-plus me-2"></i>Ajouter
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="matieresTable" class="table table-dark table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($matieres as $matiere): ?>
                                <tr>
                                    <td>
                                        <?php 
                                            $nom = htmlspecialchars($matiere['nom']);
                                            // Ajouter un retour à la ligne tous les 50 caractères
                                            echo '<span class="matiere-nom">' . 
                                                 wordwrap($nom, 50, "<br>", true) . 
                                                 '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info me-2" onclick="viewMatiere(<?= $matiere['id'] ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning me-2" onclick="editMatiere(<?= $matiere['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteMatiere(<?= $matiere['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Classe -->
    <div class="modal fade" id="classeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Classe</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="classeForm">
                        <input type="hidden" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control bg-dark text-white" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Niveau</label>
                            <select class="form-select bg-dark text-white" name="niveau" required>
                                <option value="maternelle">Maternelle</option>
                                <option value="primaire">Primaire</option>
                                <option value="college">Collège</option>
                                <option value="lycee">Lycée</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ordre</label>
                            <input type="number" class="form-control bg-dark text-white" name="ordre" required min="1">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="actif" checked>
                            <label class="form-check-label">Actif</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="classeForm" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filière -->
    <div class="modal fade" id="filiereModal" tabindex="-1" aria-labelledby="filiereModalLabel">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="filiereModalLabel">Filière</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="filiereForm">
                        <input type="hidden" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control bg-dark text-white" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Département</label>
                            <input type="text" class="form-control bg-dark text-white" name="departement" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Niveau</label>
                            <select class="form-select bg-dark text-white" name="niveau" required>
                                <option value="licence">Licence</option>
                                <option value="master">Master</option>
                                <option value="doctorat">Doctorat</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="actif" checked>
                            <label class="form-check-label">Actif</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="filiereForm" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Matière -->
    <div class="modal fade" id="matiereModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Matière</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="matiereForm">
                        <input type="hidden" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control bg-dark text-white" name="nom" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" form="matiereForm" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Fonctions globales pour les modales
function showAddClasseModal() {
    $('#classeForm')[0].reset();
    $('#classeForm [name="id"]').val('');
    $('#classeModal').modal('show');
}

function showAddFiliereModal() {
    $('#filiereForm')[0].reset();
    $('#filiereForm [name="id"]').val('');
    $('#filiereModal').modal('show');
}

// Fonction pour voir les détails d'une matière
function viewMatiere(id) {
    $.get(`get_matiere.php?id=${id}`, function(response) {
        if (response.success) {
            const matiere = response.data;
            // Formater le texte avec des retours à la ligne tous les 50 caractères
            const nomFormate = matiere.nom.match(/.{1,50}/g).join('\n');
            Swal.fire({
                title: 'Détails de la matière',
                html: `
                    <div class="text-start">
                        <p><strong>Nom :</strong></p>
                        <p class="matiere-nom">${nomFormate}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Fermer'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de récupérer les détails'
            });
        }
    });
}

// Fonction pour voir les détails d'une classe
function viewClasse(id) {
    $.get(`get_classe.php?id=${id}`, function(response) {
        if (response.success) {
            const classe = response.data;
            Swal.fire({
                title: 'Détails de la classe',
                html: `
                    <div class="text-start">
                        <p><strong>Nom :</strong> ${classe.nom}</p>
                        <p><strong>Niveau :</strong> ${classe.niveau}</p>
                        <p><strong>Ordre :</strong> ${classe.ordre}</p>
                        <p><strong>État :</strong> ${classe.actif == 1 ? 'Actif' : 'Inactif'}</p>
                        <p><strong>Date de création :</strong> ${classe.created_at || 'Non disponible'}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Fermer'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de récupérer les détails'
            });
        }
    });
}

// Fonction pour voir les détails d'une filière
function viewFiliere(id) {
    $.get(`get_filiere.php?id=${id}`, function(response) {
        if (response.success) {
            const filiere = response.data;
            Swal.fire({
                title: 'Détails de la filière',
                html: `
                    <div class="text-start">
                        <p><strong>Nom :</strong> ${filiere.nom}</p>
                        <p><strong>Département :</strong> ${filiere.departement}</p>
                        <p><strong>Niveau :</strong> ${filiere.niveau}</p>
                        <p><strong>État :</strong> ${filiere.actif == 1 ? 'Actif' : 'Inactif'}</p>
                        <p><strong>Date de création :</strong> ${filiere.created_at || 'Non disponible'}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Fermer'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de récupérer les détails'
            });
        }
    });
}

// Remplacez la configuration DataTables existante par celle-ci
$(document).ready(function() {
    const tableConfig = {
        language: {
            "sEmptyTable": "Aucune donnée disponible",
            "sInfo": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "sInfoEmpty": "Affichage de 0 à 0 sur 0 entrée",
            "sInfoFiltered": "(filtré sur _MAX_ entrées au total)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Afficher _MENU_ entrées",
            "sLoadingRecords": "Chargement...",
            "sProcessing": "Traitement...",
            "sSearch": "Rechercher :",
            "sZeroRecords": "Aucun résultat trouvé",
            "oPaginate": {
                "sFirst": "Premier",
                "sLast": "Dernier",
                "sNext": "Suivant",
                "sPrevious": "Précédent"
            }
        },
        responsive: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
        dom: '<"top"lf>rt<"bottom"ip><"clear">',
        initComplete: function () {
            // Personnalisation du champ de recherche
            $('.dataTables_filter input').addClass('form-control bg-dark text-white');
            $('.dataTables_filter input').attr('placeholder', 'Rechercher...');
            
            // Personnalisation du sélecteur de nombre de lignes
            $('.dataTables_length select').addClass('form-select bg-dark text-white');
        }
    };

    // Initialisation des tableaux avec la configuration
    $('#classesTable').DataTable({
        ...tableConfig,
        order: [[2, 'asc']] // Tri par défaut sur la colonne "Ordre"
    });

    $('#filieresTable').DataTable({
        ...tableConfig,
        order: [[1, 'asc']] // Tri par défaut sur la colonne "Département"
    });

    $('#matieresTable').DataTable({
        ...tableConfig,
        order: [[0, 'asc']] // Tri par défaut sur la colonne "Nom"
    });
});

    // Fonctions CRUD
function editClasse(id) {
    $.get('get_classe.php', {id: id})
        .done(function(data) {
            const classe = JSON.parse(data);
            $('#classeForm [name="id"]').val(classe.id);
            $('#classeForm [name="nom"]').val(classe.nom);
            $('#classeForm [name="niveau"]').val(classe.niveau);
            $('#classeForm [name="ordre"]').val(classe.ordre);
            $('#classeForm [name="actif"]').prop('checked', classe.actif == 1);
            $('#classeModal').modal('show');
        })
        .fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de récupérer les données'
            });
        });
}

function deleteClasse(id) {
    Swal.fire({
        title: 'Confirmer la suppression',
        text: 'Cette action est irréversible',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('delete_classe.php', {id: id})
                .done(function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        location.reload();
                    } else {
                        Swal.fire('Erreur', data.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Erreur', 'Erreur de communication serveur', 'error');
                });
        }
    });
}

// Fonction pour éditer une filière
function editFiliere(id) {
    fetch(`get_filiere.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Erreur lors de la récupération');
            }
            
            // Remplir le formulaire avec les données
            const filiere = data.data;
            const form = document.getElementById('filiereForm');
            form.elements['id'].value = filiere.id;
            form.elements['nom'].value = filiere.nom;
            form.elements['departement'].value = filiere.departement || '';
            form.elements['niveau'].value = filiere.niveau;
            form.elements['actif'].checked = filiere.actif == 1;

            // Afficher le modal
            const modal = new bootstrap.Modal(document.getElementById('filiereModal'));
            modal.show();
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Impossible de récupérer les données de la filière'
            });
        });
}

function deleteFiliere(id) {
    Swal.fire({
        title: 'Confirmer la suppression',
        text: 'Êtes-vous sûr de vouloir supprimer cette filière ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'delete_filiere.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.message || 'Filière supprimée avec succès',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(response.message);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Erreur lors de la suppression'
                    });
                }
            });
        }
    });
}

// Fonction pour éditer une matière
function editMatiere(id) {
    $.get(`get_matiere.php?id=${id}`, function(response) {
        if (response.success) {
            Swal.fire({
                title: 'Modifier la matière',
                html: `
                    <input id="matiere-name" class="swal2-input" value="${response.data.nom}">
                `,
                showCancelButton: true,
                confirmButtonText: 'Modifier',
                cancelButtonText: 'Annuler',
                preConfirm: () => {
                    return {
                        nom: document.getElementById('matiere-name').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'update_matiere.php',
                        type: 'POST',
                        data: {
                            id: id,
                            nom: result.value.nom
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Succès',
                                    text: 'Matière modifiée avec succès!'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        }
    });
}

function deleteMatiere(id) {
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: "Cette action est irréversible!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'delete_matiere.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: 'Matière supprimée avec succès!'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.message
                        });
                    }
                }
            });
        }
    });
}

// Gestion des formulaires
$(document).ready(function() {
    // Formulaire des filières
    $('#filiereForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const isEdit = formData.get('id') ? true : false;

        $.ajax({
            url: isEdit ? 'update_filiere.php' : 'add_filiere.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#filiereModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: isEdit ? 'Modification réussie' : 'Ajout réussi',
                        text: response.message || 'La filière a été enregistrée avec succès',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: response.message || 'Une erreur est survenue'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur de communication avec le serveur'
                });
                console.error("Erreur AJAX:", error);
            }
        });
    });

    // Formulaire des classes (même logique)
    $('#classeForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const isEdit = formData.get('id') ? true : false;

        $.ajax({
            url: isEdit ? 'update_classe.php' : 'add_classe.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#classeModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: isEdit ? 'Modification réussie' : 'Ajout réussi',
                        text: response.message || 'La classe a été enregistrée avec succès',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: response.message || 'Une erreur est survenue'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur de communication avec le serveur'
                });
            }
        });
    });

    // Formulaire des matières
    $('#matiereForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const isEdit = formData.get('id') ? true : false;

        $.ajax({
            url: isEdit ? 'update_matiere.php' : 'add_matiere.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#matiereModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: isEdit ? 'Modification réussie' : 'Ajout réussi',
                        text: response.message || 'La matière a été enregistrée avec succès',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: response.message || 'Une erreur est survenue'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur de communication avec le serveur'
                });
            }
        });
    });

    // Boutons d'ajout
    $('.btn-add').click(function() {
        const type = $(this).data('type');
        if(type === 'classe') {
            $('#classeForm')[0].reset();
            $('#classeForm [name="id"]').val('');
            $('#classeModal').modal('show');
        } else if(type === 'filiere') {
            $('#filiereForm')[0].reset();
            $('#filiereForm [name="id"]').val('');
            $('#filiereModal').modal('show');
        } else if(type === 'matiere') {
            $('#matiereForm')[0].reset();
            $('#matiereForm [name="id"]').val('');
            $('#matiereModal').modal('show');
        }
    });
});
    </script>
</body>
</html>