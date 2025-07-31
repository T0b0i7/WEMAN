<?php
require_once('../config/connexion.php');
require_once('../pages/protect-source.php');


// Récupérer les statistiques
$stmt = $pdo->query("SELECT COUNT(*) as total_documents FROM documents");
$documents_count = $stmt->fetch()['total_documents'];

$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM utilisateurs WHERE statut = 'actif'");
$users_count = $stmt->fetch()['total_users'];

$stmt = $pdo->query("SELECT COUNT(*) as total_demandes FROM demandes_redaction WHERE statut = 'en_attente'");
$demandes_count = $stmt->fetch()['total_demandes'];

// Ajouter des statistiques supplémentaires
$stmt = $pdo->query("SELECT SUM(prix) as total_revenue FROM documents WHERE MONTH(cree_a) = MONTH(CURRENT_DATE)");
$revenue_mensuel = $stmt->fetch()['total_revenue'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total_downloads FROM documents WHERE MONTH(cree_a) = MONTH(CURRENT_DATE)");
$downloads_mensuel = $stmt->fetch()['total_downloads'] ?? 0;

// Modifier la requête SQL pour récupérer les dernières demandes
$stmt = $pdo->query("
    SELECT 
        d.id,
        d.sujet_theme,
        d.description,
        d.delai_souhaite,
        d.budget,
        d.statut,
        d.date_creation,
        d.classe,
        d.matiere,
        d.filiere,
        u.prenom,
        u.nom
    FROM demandes_redaction d 
    JOIN utilisateurs u ON d.utilisateur_id = u.id 
    ORDER BY d.date_creation DESC 
    LIMIT 5
");

$derniers_demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les activités récentes
$stmt = $pdo->query("
    SELECT a.*, u.prenom, u.nom, d.titre as document_titre
    FROM activites a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    LEFT JOIN documents d ON a.document_id = d.id
    ORDER BY a.date_creation DESC
    LIMIT 5
");
$activites = $stmt->fetchAll();

// Nouvelles statistiques
// Documents téléversés aujourd'hui
$stmt = $pdo->query("SELECT COUNT(*) as uploads_today FROM documents WHERE DATE(cree_a) = CURRENT_DATE");
$uploads_today = $stmt->fetch()['uploads_today'];

// Modifier la requête pour les types de fichiers les plus téléversés
$stmt = $pdo->query("
    SELECT type_fichier, COUNT(*) as total
    FROM documents 
    GROUP BY type_fichier
    ORDER BY total DESC
    LIMIT 5
");
$file_types = $stmt->fetchAll();

// Modifier la requête pour les utilisateurs les plus actifs
// Cette requête utilise maintenant la table activites au lieu de documents
$stmt = $pdo->query("
    SELECT u.prenom, u.nom, COUNT(a.id) as total_uploads
    FROM utilisateurs u
    LEFT JOIN activites a ON u.id = a.utilisateur_id AND a.action = 'upload'
    GROUP BY u.id
    ORDER BY total_uploads DESC
    LIMIT 5
");
$active_users = $stmt->fetchAll();

// Tentatives de connexion échouées
$stmt = $pdo->query("
    SELECT COUNT(*) as failed_attempts 
    FROM activites 
    WHERE action = 'connexion_echouee' 
    AND date_creation > DATE_SUB(NOW(), INTERVAL 24 HOUR)
");
$failed_logins = $stmt->fetch()['failed_attempts'];

// Documents suspects
$stmt = $pdo->query("
    SELECT COUNT(*) as suspicious_files 
    FROM documents 
    WHERE statut = 'suspect'
");
$suspicious_files = $stmt->fetch()['suspicious_files'];

// Statistiques de téléchargement
$stmt = $pdo->query("
    SELECT 
        d.titre,
        d.downloads_count,
        c.nom as categorie
    FROM documents d
    JOIN categories_documents c ON d.categorie_id = c.id
    ORDER BY d.downloads_count DESC
    LIMIT 5
");
$top_downloads = $stmt->fetchAll();

// Total des téléchargements
$stmt = $pdo->query("SELECT SUM(downloads_count) as total_downloads FROM documents");
$total_downloads = $stmt->fetch()['total_downloads'] ?? 0;

// Calculer l'évolution des documents
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_current,
        (SELECT COUNT(*) FROM documents 
         WHERE cree_a BETWEEN DATE_SUB(NOW(), INTERVAL 2 MONTH) 
         AND DATE_SUB(NOW(), INTERVAL 1 MONTH)) as total_previous
    FROM documents 
    WHERE cree_a >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
");
$docs_stats = $stmt->fetch();
$docs_evolution = $docs_stats['total_previous'] > 0 
    ? round(($docs_stats['total_current'] - $docs_stats['total_previous']) / $docs_stats['total_previous'] * 100)
    : 0;

// Calculer les statistiques des formations
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_current,
        (SELECT COUNT(*) FROM documents 
         WHERE categorie_id IN (SELECT id FROM categories_documents WHERE nom LIKE '%Formation%')
         AND cree_a BETWEEN DATE_SUB(NOW(), INTERVAL 2 MONTH) 
         AND DATE_SUB(NOW(), INTERVAL 1 MONTH)) as total_previous
    FROM documents 
    WHERE categorie_id IN (SELECT id FROM categories_documents WHERE nom LIKE '%Formation%')
    AND cree_a >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
");
$formations_stats = $stmt->fetch();
$formations_evolution = $formations_stats['total_previous'] > 0 
    ? round(($formations_stats['total_current'] - $formations_stats['total_previous']) / $formations_stats['total_previous'] * 100)
    : 0;

// Calculer les revenus
$stmt = $pdo->query("
    SELECT 
        SUM(prix) as revenue_current,
        (SELECT SUM(prix) FROM documents 
         WHERE cree_a BETWEEN DATE_SUB(NOW(), INTERVAL 2 MONTH) 
         AND DATE_SUB(NOW(), INTERVAL 1 MONTH)) as revenue_previous
    FROM documents 
    WHERE cree_a >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
");
$revenue_stats = $stmt->fetch();
$revenue_evolution = $revenue_stats['revenue_previous'] > 0 
    ? round(($revenue_stats['revenue_current'] - $revenue_stats['revenue_previous']) / $revenue_stats['revenue_previous'] * 100)
    : 0;

// Ajout des statistiques des messages
$stmt = $pdo->query("SELECT COUNT(*) as total_messages FROM contacts");
$messages_count = $stmt->fetch()['total_messages'];

$stmt = $pdo->query("SELECT COUNT(*) as new_messages FROM contacts WHERE date_creation >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$new_messages = $stmt->fetch()['new_messages'];

// Statistiques par catégorie pour le graphique
$stmt = $pdo->query("
    SELECT cd.nom, COUNT(d.id) as total 
    FROM categories_documents cd
    LEFT JOIN documents d ON cd.id = d.categorie_id
    GROUP BY cd.id 
    ORDER BY total DESC 
    LIMIT 5
");
$categories_stats = $stmt->fetchAll();

// Statistiques mensuelles pour le graphique
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(cree_a, '%Y-%m') as month,
        COUNT(*) as total_docs,
        SUM(downloads_count) as total_downloads,
        SUM(prix) as revenue
    FROM documents 
    WHERE cree_a >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY month
    ORDER BY month
");
$monthly_stats = $stmt->fetchAll();

// Total des revenus
$stmt = $pdo->query("SELECT SUM(prix) as total_revenue FROM documents");
$total_revenue = $stmt->fetch()['total_revenue'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <title>Tableau de Bord - WEMANTCHE Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    
 
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

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 300px; /* Augmentation de la largeur */
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
            padding: 2rem; /* Plus grand padding */
            border-bottom: 1px solid var(--border-color);
            background: rgba(67, 97, 238, 0.1);
        }

        .sidebar-logo {
            width: 60px; /* Logo plus grand */
            height: 60px;
            border-radius: 12px;
        }

        .sidebar-header h5 {
            font-size: 1.5rem; /* Texte plus grand */
        }

        .sidebar-nav {
            padding: 1.5rem 0;
        }

        .nav-link {
            padding: 1rem 2rem; /* Plus grand padding */
            font-size: 1.1rem; /* Texte plus grand */
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-link i {
            font-size: 1.25rem; /* Icônes plus grandes */
            width: 30px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 300px;
            transition: all 0.3s ease;
            padding: 2rem;
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
            .sidebar-toggle {
                display: block;
            }
            
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

        /* Style pour les placeholders */
        ::-webkit-input-placeholder { /* Chrome/Safari/Opera */
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        ::-moz-placeholder { /* Firefox */
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        :-ms-input-placeholder { /* IE/Edge */
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        ::placeholder {
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        /* Style pour les inputs */
        .form-control::placeholder,
        .form-select::placeholder,
        input::placeholder,
        textarea::placeholder {
            color: var(--text-white) !important;
            opacity: 0.7 !important;
        }

        .form-control,
        .form-select {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-white) !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25) !important;
        }

        /* Style pour les titres */
        .card-title, 
        .card-header h5,
        .table th,
        .kpi-header h5 {
            color: var(--text-white) !important;
            font-weight: 500;
        }

        /* Style pour les en-têtes de tableau */
        .table thead th {
            color: var(--text-white) !important;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--bg-card);
            font-weight: 500;
        }

        /* Style pour les titres de sections */
        .section-title,
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-white) !important;
        }

        /* Style pour les en-têtes de graphiques */
        .apexcharts-title-text,
        .apexcharts-legend-text {
            color: var(--text-white) !important;
        }

        /* Style pour les labels des graphiques */
        .apexcharts-text,
        .apexcharts-legend-text {
            color: var(--text-white) !important;
        }

        /* Style pour améliorer la lisibilité des titres */
        .card-header {
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-card);
        }
</style>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center gap-2">
                    <img src="../assets/images/WEMANTCHE LOGO p 2.png" alt="Logo" class="sidebar-logo">
                    <h5 class="mb-0">WEMANTCHE</h5>
                </div>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item active">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="categories/index.php" class="nav-link">
                        <i class="fas fa-folder"></i>
                        <span>Catégories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="documents/index.php" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span>Documents</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="redaction/demandes.php" class="nav-link">
                        <i class="fas fa-pen-fancy"></i>
                        <span>Demandes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="contacts/index.php" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span>Messages</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users/index.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
            </ul>
        </nav>

        <main class="main-content">
            <!-- Header -->
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Tableau de bord</h1>
                <div class="header-actions">
                    <a href="generate_report.php" class="btn btn-light">
                        <i class="fas fa-download me-2"></i>Télécharger le rapport
                    </a>
                </div>
            </div>

            <div class="container-fluid py-4">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <div class="stat-trend <?php echo $docs_evolution >= 0 ? 'up' : 'down'; ?>">
                                <i class="fas fa-arrow-<?php echo $docs_evolution >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo abs($docs_evolution); ?>%</span>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3 class="stat-value"><?php echo number_format($documents_count); ?></h3>
                            <p class="stat-label">Documents totaux</p>
                        </div>
                        <div class="stat-footer">
                            <small>+<?php echo $docs_stats['total_current']; ?> ce mois</small>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($users_count); ?></div>
                        <div class="stat-label">Utilisateurs actifs</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-coins text-white"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($total_revenue, 0, ',', ' '); ?> FCFA</div>
                        <div class="stat-label">Revenus totaux des documents</div>
                    </div>

                    <!-- Nouvelle carte pour les téléversements du jour -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-upload text-white"></i>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3 class="stat-value"><?php echo $uploads_today; ?></h3>
                            <p class="stat-label">Téléversements aujourd'hui</p>
                        </div>
                    </div>

                    <!-- Nouvelle carte pour les téléchargements totaux -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-download"></i>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3 class="stat-value"><?php echo number_format($total_downloads); ?></h3>
                            <p class="stat-label">Total Téléchargements</p>
                        </div>
                    </div>

                    <!-- Nouvelle carte pour les messages -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3 class="stat-value"><?php echo number_format($messages_count); ?></h3>
                            <p class="stat-label">Messages reçus</p>
                        </div>
                        <?php if($new_messages > 0): ?>
                        <div class="stat-footer">
                            <span class="badge bg-success">
                                +<?php echo $new_messages; ?> nouveaux
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Stats des demandes de rédaction -->
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-pen-fancy text-white"></i>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3 class="stat-value"><?php echo number_format($demandes_count); ?></h3>
                            <p class="stat-label">Demandes de Rédaction</p>
                        </div>
                    </div>
                </div>

             

                <!-- Dernières demandes de rédaction -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Dernières Demandes de Rédaction</h5>
                                <a href="redaction/demandes.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-list me-2"></i>Voir toutes les demandes
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Client</th>
                                                <th>Sujet</th>
                                                <th>Description</th>
                                                <th>Délai</th>
                                                <th>Budget</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($derniers_demandes as $demande): ?>
                                            <tr>
                                                <td><?php echo $demande['id']; ?></td>
                                                <td><?php echo htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($demande['sujet_theme']); ?></td>
                                                <td><?php echo substr(htmlspecialchars($demande['description']), 0, 50) . '...'; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($demande['delai_souhaite'])); ?></td>
                                                <td><?php echo number_format($demande['budget'], 0, ',', ' ') . ' FCFA'; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($demande['date_creation'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $demande['statut'] === 'en_attente' ? 'warning' : 
                                                        ($demande['statut'] === 'en_cours' ? 'info' : 
                                                        ($demande['statut'] === 'termine' ? 'success' : 'danger')); ?>">
                                                        <?php echo ucfirst($demande['statut']); ?>
                                                    </span>
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

                <!-- Graphiques -->
                <div class="row g-4 mt-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Évolution mensuelle</h5>
                                <div id="monthlyChart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Top Catégories</h5>
                                <div id="categoriesChart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ajouter une nouvelle section pour les KPI détaillés -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <h5>Performance mensuelle</h5>
                                <div class="kpi-actions">
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="kpi-body">
                                <div id="performanceChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="kpi-card">
                            <div class="kpi-header">
                                <h5>Répartition des documents</h5>
                            </div>
                            <div class="kpi-body">
                                <div id="documentsPieChart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nouvelle section pour les analyses détaillées -->
                <div class="row mt-4">
                    <!-- Types de fichiers -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Types de fichiers populaires</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($file_types as $type): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($type['type_fichier']); ?></td>
                                                <td><?php echo $type['total']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Utilisateurs actifs -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Utilisateurs les plus actifs</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Utilisateur</th>
                                                <th>Téléversements</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($active_users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></td>
                                                <td><?php echo $user['total_uploads']; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="../assets/js/admin/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Configuration des graphiques
        const monthlyData = <?php echo json_encode($monthly_stats); ?>;
        const categoriesData = <?php echo json_encode($categories_stats); ?>;

        // Graphique d'évolution mensuelle
        new ApexCharts(document.querySelector("#monthlyChart"), {
            chart: {
                type: 'line',
                height: 300
            },
            series: [{
                name: 'Documents',
                data: monthlyData.map(item => item.total_docs)
            }, {
                name: 'Téléchargements',
                data: monthlyData.map(item => item.total_downloads)
            }],
            xaxis: {
                categories: monthlyData.map(item => {
                    const date = new Date(item.month + '-01');
                    return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                })
            }
        }).render();

        // Graphique des catégories
        new ApexCharts(document.querySelector("#categoriesChart"), {
            chart: {
                type: 'donut',
                height: 300
            },
            series: categoriesData.map(item => item.total),
            labels: categoriesData.map(item => item.nom),
            legend: {
                position: 'bottom'
            }
        }).render();
    </script>
    <script src="system-update-notification.js"></script>
</body>
</html>