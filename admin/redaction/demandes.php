<?php
require_once '../../config/connexion.php';

try {
    // Récupération des demandes
    $sql = "SELECT 
    dr.*,
    u.nom, 
    u.prenom,
    cd.nom as categorie_nom
    FROM demandes_redaction dr 
    LEFT JOIN utilisateurs u ON dr.utilisateur_id = u.id 
    LEFT JOIN categories_documents cd ON dr.categorie_id = cd.id
    ORDER BY dr.date_creation DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes - WEMANTCHE Admin</title>
    <link rel="icon" type="image/jpg" href="../../assets/images/WEMANTCHE LOGO p 2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="../../assets/css/admin.css" rel="stylesheet">
  
</head>
<style>
        /* Stats Cards Styling */
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
            background: linear-gradient(145deg, #f59e0b20, #f59e0b40);
            border-left: 4px solid var(--warning);
        }

        .stats-card:nth-child(3) {
            background: linear-gradient(145deg, #2dd4bf20, #2dd4bf40);
            border-left: 4px solid var(--success);
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
            color: white; /* Ajout de la couleur blanche pour les icônes */
        }

        .stats-value {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-white), #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .stats-label {
          color: var(--text-white); /* Changé en blanc */
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        /* Style des icônes */
        .stats-icon.bg-primary { 
            background: var(--primary);
        }

        .stats-icon.bg-warning { 
            background: var(--warning);
        }

        .stats-icon.bg-success { 
            background: var(--success);
        }

        /* Styles Modal */
        .modal-content {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: rgba(67, 97, 238, 0.1);
            border-bottom: 1px solid var(--border-color);
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

        /* Formulaires */
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

        /* Boutons */
        .btn-secondary {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
        }

        .btn-secondary:hover {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-white);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(145deg, var(--primary), #2d3fff);
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #2d3fff, var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        /* Layout et Sidebar */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 300px; /* Augmentation de la largeur */
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease-in-out; /* Transition fluide */
        }

        .sidebar-header {
            padding: 1.8rem; /* Padding plus généreux */
            border-bottom: 1px solid var(--border-color);
            background: rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            gap: 1.5rem; /* Plus d'espace entre logo et texte */
        }

        .sidebar-logo {
            width: 50px; /* Logo plus grand */
            height: 50px;
            border-radius: 10px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-header h5 {
            color: var(--text-white);
            font-weight: 600;
            font-size: 1.4rem; /* Police plus grande */
            margin: 0;
            letter-spacing: 0.5px;
        }

        .sidebar-nav {
            padding: 1.5rem 0;
            list-style: none;
            margin: 0;
        }

        .nav-item {
            margin: 0.8rem 0; /* Plus d'espace entre les items */
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 15px; /* Plus d'espace entre icône et texte */
          color: var(--text-white); /* Changé en blanc */
            padding: 1rem 1.8rem; /* Padding plus généreux */
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-size: 1.1rem; /* Police plus grande */
            font-weight: 500;
        }

        .sidebar-nav .nav-link i {
            width: 24px; /* Icônes plus grandes */
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: var(--text-white);
            background: rgba(67, 97, 238, 0.1);
            border-left-color: var(--primary);
            transform: translateX(5px); /* Effet de décalage au hover */
        }

        .sidebar-nav .nav-link:hover i,
        .sidebar-nav .nav-link.active i {
            color: var(--primary);
            transform: scale(1.1);
        }

        /* Main Content ajustement */
        .main-content {
            flex: 1;
            margin-left: 300px; /* Ajusté à la nouvelle largeur de la sidebar */
            padding: 2rem;
            background: var(--bg-dark);
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                transition: margin-left 0.3s ease-in-out;
            }
        }

        /* Style de base */
        body {
            background: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Pour aligner le logo et le texte */
        .sidebar-header h5 {
            color: var(--text-white);
            margin: 0;
            font-size: 1.1rem;
        }

        /* Style pour les tableaux */
        .table {
            color: var(--text-white);
            margin-bottom: 0;
        }

        .table th {
            background: rgba(67, 97, 238, 0.1);
          color: var(--text-white); /* Changé en blanc */
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table thead th {
            color: white;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Style pour les badges */
        .badge {
            padding: 0.5em 1em;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Style pour les boutons d'action */
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-sm i {
            font-size: 0.95rem;
        }

        .btn-sm:hover {
            transform: translateY(-2px);
        }

        /* Personnalisation de la barre de recherche */
        .input-group-text {
            background: var(--bg-card);
            border-color: var(--border-color);
          color: var(--text-white); /* Changé en blanc */
        }

        .form-control {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-white);
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: var(--bg-card);
            border-color: var(--primary);
            color: var(--text-white);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
    </style>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <!-- Copier la sidebar du dashboard ici -->
            <div class="sidebar-header">
                <img src="../../assets/images/WEMANTCHE LOGO p 2.png " alt="Logo" class="sidebar-logo">
                <h5 class="mb-0">WEMANTCHE</h5>
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
                <!-- En-tête -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Gestion des Demandes de Rédaction</h1>
                </div>

                <!-- Statistiques -->
                <div class="stats-wrapper">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="ms-3">
                                <div class="stats-label">Total Demandes</div>
                                <div class="stats-value" id="totalDemandes">0</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="ms-3">
                                <div class="stats-label">En Attente</div>
                                <div class="stats-value" id="pendingDemandes">0</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="ms-3">
                                <div class="stats-label">Terminées</div>
                                <div class="stats-value" id="completedDemandes">0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recherche -->
                <div class="input-group mb-4">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un document...">
                </div>

                <!-- Liste des demandes -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="demandesTable">
                                <thead>
                                    <tr style="color: white;">
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Catégorie</th>
                                        <th>Sujet/Thème</th>
                                        <th>Filière/Classe</th>
                                        <th>Matière</th>
                                        <th>Budget</th>
                                        <th>Délai</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($result): foreach($result as $row): 
                                    $statut_class = [
                                        'en_attente' => 'warning',
                                        'en_cours' => 'info',
                                        'termine' => 'success',
                                        'annule' => 'danger',
                                        'rejetee' => 'danger',
                                        'validee' => 'success'
                                    ];
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></td>
                                        <td><?= htmlspecialchars($row['categorie_nom']) ?></td>
                                        <td><?= htmlspecialchars($row['sujet_theme']) ?></td>
                                        <td>
                                            <?php if($row['filiere']): ?>
                                                <span class="badge bg-info">Filière: <?= htmlspecialchars($row['filiere']) ?></span>
                                            <?php endif; ?>
                                            <?php if($row['classe']): ?>
                                                <span class="badge bg-primary">Classe: <?= htmlspecialchars($row['classe']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $row['matiere'] ? htmlspecialchars($row['matiere']) : '-' ?></td>
                                        <td><?= number_format($row['budget'], 0, ',', ' ') ?> FCFA</td>
                                        <td>
                                            <div>Créé le: <?= date('d/m/Y', strtotime($row['date_creation'])) ?></div>
                                            <div>Deadline: <?= date('d/m/Y', strtotime($row['delai_souhaite'])) ?></div>
                                        </td>
                                        <td>
                                            <span class='badge bg-<?= $statut_class[$row['statut']] ?>'>
                                                <?= str_replace('_', ' ', ucfirst($row['statut'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class='btn btn-sm btn-info view-btn' data-id='<?= $row['id'] ?>'
                                                    data-bs-toggle="tooltip" title="Voir les détails">
                                                <i class='fas fa-eye'></i>
                                            </button>
                                            <button class='btn btn-sm btn-success edit-btn' data-id='<?= $row['id'] ?>'
                                                    data-bs-toggle="tooltip" title="Modifier">
                                                <i class='fas fa-edit'></i>
                                            </button>
                                            <button class='btn btn-sm btn-danger delete-btn' data-id='<?= $row['id'] ?>'
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class='fas fa-trash'></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modales -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de la Demande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>Client :</strong> <span id="view-client"></span></p>
                            <p><strong>Catégorie :</strong> <span id="view-categorie"></span></p>
                            <p><strong>Sujet/Thème :</strong> <span id="view-sujet"></span></p>
                            <p><strong>Filière :</strong> <span id="view-filiere"></span></p>
                            <p><strong>Classe :</strong> <span id="view-classe"></span></p>
                            <p><strong>Matière :</strong> <span id="view-matiere"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Budget :</strong> <span id="view-budget"></span></p>
                            <p><strong>Date création :</strong> <span id="view-date-creation"></span></p>
                            <p><strong>Deadline :</strong> <span id="view-deadline"></span></p>
                            <p><strong>Dernière modification :</strong> <span id="view-date-modification"></span></p>
                            <p><strong>Statut :</strong> <span id="view-statut"></span></p>
                        </div>
                        <div class="col-12">
                            <p><strong>Objectifs :</strong></p>
                            <p id="view-objectifs"></p>
                            <p><strong>Plan souhaité :</strong></p>
                            <p id="view-plan"></p>
                            <p><strong>Consignes spécifiques :</strong></p>
                            <p id="view-consignes"></p>
                            <p><strong>Description générale :</strong></p>
                            <p id="view-description"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="replyButton">Répondre</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le Statut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit-id">
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Statut</label>
                        <select class="form-select" id="edit-status">
                            <option value="en_attente">En attente</option>
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="saveEdit">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la Suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer cette demande ?</p>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Configuration de DataTables
            const table = $('#demandesTable').DataTable({
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    "emptyTable": "Aucune donnée disponible",
                    "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    "infoEmpty": "Affichage de 0 à 0 sur 0 entrées",
                    "infoFiltered": "(filtré sur _MAX_ entrées totales)",
                    "lengthMenu": "Afficher _MENU_ entrées par page",
                    "loadingRecords": "Chargement...",
                    "processing": "Traitement...",
                    "search": "<i class='fas fa-search'></i> Rechercher :",
                    "zeroRecords": "Aucun résultat trouvé",
                    "paginate": {
                        "first": "<i class='fas fa-angle-double-left'></i>",
                        "last": "<i class='fas fa-angle-double-right'></i>",
                        "next": "<i class='fas fa-angle-right'></i>",
                        "previous": "<i class='fas fa-angle-left'></i>"
                    }
                },
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
                order: [[0, 'desc']], // Tri par défaut sur la première colonne (ID) en descendant
                columnDefs: [
                    {
                        targets: -1, // Dernière colonne (actions)
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Style personnalisé pour la recherche
            $('.dataTables_filter input').addClass('form-control form-control-sm');
            $('.dataTables_filter input').attr('placeholder', 'Rechercher...');
            
            // Style personnalisé pour le sélecteur d'entrées
            $('.dataTables_length select').addClass('form-select form-select-sm');

            // Comptage des demandes par statut
            function updateStats() {
                const tableRows = $('#demandesTable tbody tr').length;
                let enAttente = 0;
                let termine = 0;
                let annule = 0;

                $('#demandesTable tbody tr').each(function() {
                    const statut = $(this).find('td:nth-child(8) .badge').text().trim().toLowerCase();
                    
                    if (statut === 'en attente') enAttente++;
                    else if (statut === 'terminé') termine++;
                    else if (statut === 'annulé') annule++;
                });

                $('#totalDemandes').text(tableRows || '0');
                $('#pendingDemandes').text(enAttente || '0');
                $('#completedDemandes').text(termine || '0');
            }

            // Appel initial pour mettre à jour les statistiques
            updateStats();

            // Mettre à jour les stats après chaque action (édition/suppression)
            $('#saveEdit, #confirmDelete').click(function() {
                setTimeout(updateStats, 1000); // Mise à jour après rechargement
            });

            let currentId = null;

            // Gestionnaire pour le bouton Voir
            $('.view-btn').click(function() {
                const id = $(this).data('id');
                window.location.href = `voir_demande.php?id=${id}`;
            });

            // Gestionnaire pour le bouton Éditer
            $('.edit-btn').click(function() {
                const id = $(this).data('id');
                
                $.ajax({
                    url: 'get_demande.php',
                    method: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            Swal.fire('Erreur', response.error, 'error');
                            return;
                        }
                        
                        $('#edit-id').val(response.id);
                        $('#edit-status').val(response.statut);
                        $('#edit-description').val(response.description);
                        $('#edit-budget').val(response.budget);
                        $('#edit-delai').val(response.delai_souhaite?.split(' ')[0]); // Prend juste la date
                        
                        $('#editModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX:', error);
                        Swal.fire('Erreur', 'Erreur lors de la récupération des données', 'error');
                    }
                });
            });

            // Gestionnaire de sauvegarde
            $('#saveEdit').click(function() {
                const formData = {
                    id: $('#edit-id').val(),
                    status: $('#edit-status').val()
                };

                $.ajax({
                    url: 'update_demande.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            Swal.fire('Erreur', response.error, 'error');
                            return;
                        }
                        
                        $('#editModal').modal('hide');
                        
                        if (formData.status === 'termine') {
                            $.ajax({
                                url: 'get_client_email.php',
                                method: 'GET',
                                data: { id: formData.id },
                                success: function(emailResponse) {
                                    const data = JSON.parse(emailResponse);
                                    if (data.email) {
                                        Swal.fire({
                                            title: 'Demande terminée',
                                            text: 'Voulez-vous envoyer le produit fini au client par email ?',
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonText: 'Oui, ouvrir Gmail',
                                            cancelButtonText: 'Non, plus tard'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // Ouvrir Gmail avec le mail pré-rempli
                                                const subject = 'Votre demande est terminée - WEMANTCHE';
                                                const body = `Bonjour,\n\nVotre demande a été traitée. Veuillez trouver ci-joint le document demandé.\n\nCordialement,\nL'équipe WEMANTCHE`;
                                                window.open(`https://mail.google.com/mail/?view=cm&fs=1&to=${data.email}&su=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`, '_blank');
                                            }
                                            location.reload();
                                        });
                                    }
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: 'Demande mise à jour avec succès',
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        }
                    }
                });
            });

            // Gestionnaire pour le bouton Supprimer
            $('.delete-btn').click(function() {
                currentId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            // Gestionnaire pour la confirmation de suppression
            $('#confirmDelete').click(function() {
                $.ajax({
                    url: 'delete_demande.php',
                    method: 'POST',
                    data: { id: currentId },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        // Recharger la page pour voir les changements
                        location.reload();
                    }
                });
            });

            // Fonctions utilitaires
            function getStatusClass(status) {
                const classes = {
                    'en_attente': 'warning',
                    'en_cours': 'info',
                    'termine': 'success',
                    'annule': 'danger'
                };
                return classes[status] || 'secondary';
            }

            function formatStatus(status) {
                return status.replace('_', ' ')
                            .split(' ')
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(' ');
            }

            // Fonction pour jouer un son de notification
            function playNotificationSound() {
                const audio = new Audio('../../assets/sounds/notification.mp3');
                audio.play();
            }

            // Fonction modifiée pour charger les notifications
            function loadNotifications() {
                $.ajax({
                    url: 'check_notifications.php',
                    method: 'GET',
                    success: function(response) {
                        const data = JSON.parse(response);
                        const notifications = data.notifications;
                        
                        // Si il y a de nouvelles demandes
                        if (data.newDemandes > 0) {
                            playNotificationSound();
                            showNotification(`${data.newDemandes} nouvelle(s) demande(s) reçue(s)`, 'info');
                            // Recharger la page pour afficher les nouvelles demandes
                            setTimeout(() => location.reload(), 3000);
                        }
                        
                        // Mise à jour du compteur
                        $('#notifCount').text(notifications.length);
                        if (notifications.length === 0) {
                            $('#notifCount').hide();
                        } else {
                            $('#notifCount').show();
                        }
                        
                        // Mise à jour de la liste des notifications
                        const notifList = $('#notificationsList');
                        notifList.empty();
                        
                        if (notifications.length === 0) {
                            notifList.append('<div class="dropdown-item">Aucune nouvelle notification</div>');
                        } else {
                            notifications.forEach(notif => {
                                const time = new Date(notif.created_at).toLocaleString();
                                const bgClass = notif.type === 'new_request' ? 'bg-info' : '';
                                notifList.append(`
                                    <div class="notification-item unread ${bgClass}" data-id="${notif.id}">
                                        <div class="d-flex justify-content-between">
                                            <div class="notification-text">${notif.message}</div>
                                            <small class="notification-time">${time}</small>
                                        </div>
                                    </div>
                                `);
                            });
                        }
                    }
                });
            }

            // Réduire l'intervalle de vérification à 10 secondes pour une meilleure réactivité
            loadNotifications();
            setInterval(loadNotifications, 10000);

            // Fonction pour afficher une notification toast
            function showNotification(message, type = 'success') {
                const toast = $(`
                    <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `);
                
                if (!$('.toast-container').length) {
                    $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
                }
                
                $('.toast-container').append(toast);
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                toast.on('hidden.bs.toast', () => toast.remove());
            }

            // Fonction pour charger les notifications
            function loadNotifications() {
                $.ajax({
                    url: 'check_notifications.php',
                    method: 'GET',
                    success: function(response) {
                        const data = JSON.parse(response);
                        const notifications = data.notifications;
                        
                        // Mise à jour du compteur
                        $('#notifCount').text(notifications.length);
                        if (notifications.length === 0) {
                            $('#notifCount').hide();
                        } else {
                            $('#notifCount').show();
                        }
                        
                        // Mise à jour de la liste des notifications
                        const notifList = $('#notificationsList');
                        notifList.empty();
                        
                        if (notifications.length === 0) {
                            notifList.append('<div class="dropdown-item">Aucune nouvelle notification</div>');
                        } else {
                            notifications.forEach(notif => {
                                const time = new Date(notif.created_at).toLocaleString();
                                notifList.append(`
                                    <div class="notification-item unread" data-id="${notif.id}">
                                        <div class="d-flex justify-content-between">
                                            <div class="notification-text">${notif.message}</div>
                                            <small class="notification-time">${time}</small>
                                        </div>
                                    </div>
                                `);
                            });
                        }
                    }
                });
            }

            // Charger les notifications toutes les 30 secondes
            loadNotifications();
            setInterval(loadNotifications, 30000);

            // Marquer une notification comme lue
            $(document).on('click', '.notification-item', function() {
                const notifId = $(this).data('id');
                $.ajax({
                    url: 'mark_notification.php',
                    method: 'POST',
                    data: { id: notifId },
                    success: function() {
                        loadNotifications();
                    }
                });
            });

            // Marquer toutes les notifications comme lues
            $('#markAllRead').click(function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'mark_all_read.php',
                    method: 'POST',
                    success: function() {
                        loadNotifications();
                    }
                });
            });

            $('#replyButton').click(function() {
                const email = $('#replyEmail').attr('href').replace('mailto:', '');
                window.location.href = `mailto:${email}`;
            });
        });

        function viewDemande(demande) {
            Swal.fire({
                title: `Demande de ${demande.prenom} ${demande.nom}`,
                html: `
                    <div class="text-start">
                        <p><strong>Sujet:</strong> ${demande.sujet}</p>
                        <p><strong>Email:</strong> ${demande.email}</p>
                        <p><strong>Date:</strong> ${new Date(demande.date_creation).toLocaleString('fr-FR')}</p>
                        <p><strong>Statut:</strong> 
                            <span class="badge bg-${getStatusClass(demande.statut)}">
                                ${formatStatus(demande.statut)}
                            </span>
                        </p>
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p>${demande.description.replace(/\n/g, '<br>')}</p>
                    </div>
                `,
                width: '600px',
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: 'Répondre',
                cancelButtonText: 'Fermer',
                customClass: {
                    content: 'text-start'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `mailto:${demande.email}`;
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            // Écouteur d'événement pour le champ de recherche
            $('#searchInput').on('input', function() {
                const searchValue = $(this).val().toLowerCase(); // Récupère la valeur saisie en minuscule
                $('#demandesTable tbody tr').filter(function() {
                    // Affiche ou masque les lignes en fonction de la correspondance
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
                });
            });
        });
    </script>

    <style>
        /* Styles personnalisés pour la pagination */
        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .page-link {
            color: #0d6efd;
            padding: 0.5rem 0.75rem;
        }
        .page-link:hover {
            color: #0a58ca;
            background-color: #e9ecef;
        }
        .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_filter input {
            margin-left: 0.5rem;
        }
        .dataTables_length select {
            min-width: 5rem;
        }
        /* Style pour les icônes dans la pagination */
        .page-link i {
            font-size: 0.875rem;
        }
        
        /* Styles pour les notifications */
        .notification-item {
            padding: .5rem 1rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: #e9ecef;
        }
        
        .notification-item .notification-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        #notifCount {
            font-size: 0.7rem;
            padding: 0.25rem 0.4rem;
        }

        /* Style pour les notifications de nouvelles demandes */
        .notification-item.bg-info {
            background-color: #cff4fc;
            border-left: 4px solid #0dcaf0;
        }
    </style>
</body>
</html>