<?php
require_once('../config/connexion.php');
// Initialiser la variable settings
$settings = [];

// Charger les paramètres (exemple avec une base de données)
function loadSettings() {
    global $settings;
    try {
        $db = new PDO("mysql:host=localhost;dbname=weman", "username", "password");
        $stmt = $db->query("SELECT * FROM settings");
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // En cas d'erreur, initialiser avec des valeurs par défaut
        $settings = [
            'site_name' => 'WEMANTCHE',
            'contact_email' => 'contact@wemantche.com',
            'description' => 'Description du site',
            'default_language' => 'fr',
            'timezone' => 'UTC',
            'setting1' => 'default1',
            'setting2' => 'default2',
            'setting3' => 'default3'
        ];
    }
}

// Charger les paramètres avant utilisation
loadSettings();

// Fonction helper pour accéder aux paramètres de manière sécurisée
function getSetting($key, $default = null) {
    global $settings;
    return isset($settings[$key]) ? $settings[$key] : $default;
}

// Remplacer les accès directs par la fonction helper
$value1 = getSetting('setting1');
$value2 = getSetting('setting2');
$value3 = getSetting('setting3');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - WEMANTCHE Admin</title>
    <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
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

        /* Sidebar - Style mis à jour */
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
            color: var(--text-white); /* Changé de text-light à text-white */
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
            color: var(--text-white); /* Ajout de la couleur blanche pour les icônes */
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-white);
            background: rgba(67, 97, 238, 0.1);
            border-left-color: var(--primary);
            transform: translateX(5px);
        }

        .nav-link span {
            color: var(--text-white); /* Ajout de la couleur blanche pour le texte */
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
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: rgba(67, 97, 238, 0.1);
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            color: var(--text-white);
            font-weight: 600;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--bg-card), var(--primary));
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
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
            background: rgba(255, 255, 255, 0.2);
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
            color: var(--text-white);
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
            color: var (--text-light);
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            border-bottom: 1px solid var (--border-color);
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

        .btn-secondary {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
          color: var(--text-white); /* Changé en blanc */
        }

        .btn-secondary:hover {
            background: var(--bg-dark);
            color: var(--text-white);
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

        /* Styles pour l'en-tête et les statistiques */
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

        /* Styles pour les onglets */
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
        }

        .nav-tabs .nav-link {
          color: var(--text-white); /* Changé en blanc */
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: var(--text-white);
            border-bottom-color: var(--primary);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            border-bottom-color: var(--primary);
        }

        /* Style des formulaires */
        .form-control, .form-select {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
        }

        .form-control:focus, .form-select:focus {
            background: var(--bg-dark);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            color: var(--text-white);
        }

        /* Style des checkboxes */
        .form-check-input {
            background-color: var(--bg-dark);
            border-color: var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
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

        /* Styles pour les formulaires de paramètres */
        .tab-content {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .tab-pane .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.25rem;
            color: var (--primary);
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: var(--text-white);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            color: var(--text-white);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: var(--bg-dark);
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.25);
        }

        .form-check-label {
          color: var(--text-white); /* Changé en blanc */
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            margin-right: 0.5rem;
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Style des boutons d'action */
        .btn-group {
            margin-top: 2rem;
            padding: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
          color: var(--text-white); /* Changé en blanc */
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--text-white);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.35);
        }

        /* Styles pour la déconnexion */
        .swal2-popup {
            border: 1px solid var(--border-color) !important;
        }

        .swal2-title, .swal2-html-container {
            color: var(--text-white) !important;
        }

        .swal2-timer-progress-bar {
            background: var(--primary) !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #ff0000);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(244, 63, 94, 0.35);
        }
    </style>
<body>
    <div class="admin-wrapper">
    <nav class="sidebar">
            <!-- Copier la sidebar du dashboard ici -->
            <div class="sidebar-header d-flex align-items-center gap-3">
                 <img src="../assets/images/WEMANTCHE LOGO p 2.png" alt="Logo" class="sidebar-logo me-3">
                <h5 class="mb-0">WEMANTCHE</h5>
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
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Paramètres</h1>
            </div>

            <div class="container-fluid py-4">
                <!-- Navigation des paramètres -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#general">
                            <i class="fas fa-cog me-2"></i>
                            <span>Général</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#notifications">
                            <i class="fas fa-bell me-2"></i>
                            <span>Notifications</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#payment">
                            <i class="fas fa-credit-card me-2"></i>
                            <span>Paiement</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link d-flex align-items-center text-danger" data-bs-toggle="tab" data-bs-target="#logout">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            <span>Déconnexion</span>
                        </button>
                    </li>
                </ul>

                <!-- Contenu des paramètres -->
                <div class="tab-content">
                    <!-- Paramètres généraux -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Paramètres généraux</h5>
                                <form id="generalSettingsForm" action="process/update_settings.php" method="POST">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Nom du site</label>
                                            <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars(getSetting('site_name', 'WEMANTCHE')); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email de contact</label>
                                            <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars(getSetting('contact_email', 'contact@wemantche.com')); ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars(getSetting('description', '')); ?></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Langue par défaut</label>
                                            <select name="default_language" class="form-select">
                                                <option value="fr" <?php echo (getSetting('default_language', 'fr') == 'fr') ? 'selected' : ''; ?>>Français</option>
                                                <option value="en" <?php echo (getSetting('default_language', 'fr') == 'en') ? 'selected' : ''; ?>>English</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Fuseau horaire</label>
                                            <select name="timezone" class="form-select">
                                                <option value="UTC" <?php echo (getSetting('timezone', 'UTC') == 'UTC') ? 'selected' : ''; ?>>UTC</option>
                                                <option value="UTC+1" <?php echo (getSetting('timezone', 'UTC') == 'UTC+1') ? 'selected' : ''; ?>>UTC+1</option>
                                            </select>
                                        </div>
                                    </div>
                                   
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Paramètres de notifications -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Notifications</h5>
                                <form id="notificationSettingsForm">
                                    <div class="mb-4">
                                        <label class="form-label">Notifications par email</label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" checked>
                                            <label class="form-check-label">Nouveaux documents</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" checked>
                                            <label class="form-check-label">Nouvelles inscriptions</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked>
                                            <label class="form-check-label">Rapports hebdomadaires</label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Paramètres de paiement -->
                    <div class="tab-pane fade" id="payment">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Configuration des paiements</h5>
                                <form id="paymentSettingsForm">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Mode de paiement par défaut</label>
                                            <select class="form-select">
                                                <option value="orange">Orange Money</option>
                                                <option value="mtn">MTN Mobile Money</option>
                                                <option value="card">Carte bancaire</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Devise</label>
                                            <select class="form-select">
                                                <option value="XOF">FCFA (XOF)</option>
                                                <option value="EUR">Euro (EUR)</option>
                                                <option value="USD">Dollar (USD)</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Déconnexion -->
                    <div class="tab-pane fade" id="logout">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4 text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </h5>
                                <p class="text-light mb-4">Vous êtes sur le point de vous déconnecter de votre session.</p>
                                <button type="button" class="btn btn-danger" onclick="handleLogout()">
                                    <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2">Annuler</button>
                    <button type="button" class="btn btn-primary" id="saveSettings">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin/settings.js"></script>
    <script>
function handleLogout() {
    fetch('logout.php')
        .then(() => {
            Swal.fire({
                title: 'Au revoir ! 👋',
                text: 'Déconnexion en cours...',
                icon: 'success',
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                background: 'var(--bg-card)',
                color: 'var(--text-white)',
                willClose: () => {
                    window.location.href = '../pages/login.php';
                }
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            // En cas d'erreur, forcer la redirection
            window.location.href = '../pages/login.php';
        });
}

// Gestionnaire d'événement pour le bouton de déconnexion
document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.querySelector('#logout .btn-danger');
    if (logoutBtn) {
        logoutBtn.onclick = (e) => {
            e.preventDefault();
            handleLogout();
        };
    }
});
</script>
</body>
</html>

