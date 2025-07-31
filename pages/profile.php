<?php
// Démarrer la session et inclure les fichiers requis
session_start();
require_once '../config/connexion.php';
require_once '../includes/auth.php';
require_once 'protect-source.php';

// Vérification de l'authentification
checkAuth();

// Récupération des informations de l'utilisateur
$stmt = $pdo->prepare("SELECT id, prenom, nom, email, telephone, role, statut FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur n'existe pas
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Initialiser les variables pour les messages
$message = '';
$success_message = '';

// Traitement des formulaires POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traitement du changement de mot de passe
    if (isset($_POST['change_password'])) {
        if ($_SESSION['changement_mdp'] >= 3) {
            $message = "Vous avez atteint la limite de 3 changements de mot de passe pour cette session.";
        } else {
            $nouveau_mot_de_passe = password_hash($_POST['nouveau_mot_de_passe'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe_hash = ? WHERE id = ?");
            $stmt->execute([$nouveau_mot_de_passe, $_SESSION['user_id']]);
            $_SESSION['changement_mdp']++;
            $message = "Mot de passe modifié avec succès!";
        }
    }

    // Traitement de la mise à jour des informations personnelles
    if (isset($_POST['prenom'], $_POST['nom'], $_POST['email'])) {
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $telephone = isset($_POST['full_phone']) ? $_POST['full_phone'] : $user['telephone'];

        $stmt = $pdo->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, email = ?, telephone = ? WHERE id = ?");
        if($stmt->execute([$prenom, $nom, $email, $telephone, $_SESSION['user_id']])) {
            $success_message = "Informations mises à jour avec succès!";
            // Recharger les informations utilisateur après la mise à jour
            $stmt = $pdo->prepare("SELECT id, prenom, nom, email, telephone, role, statut FROM utilisateurs WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    // Traitement de la déconnexion
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit();
    }

    // Traitement de la suppression de compte
    if (isset($_POST['delete_account'])) {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        session_destroy();
        header('Location: register.php');
        exit();
    }
}

// Initialisation de la variable de session pour le suivi des changements de mot de passe
if (!isset($_SESSION['changement_mdp'])) {
    $_SESSION['changement_mdp'] = 0;
}

// Commencer le HTML ici
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - WEMANTCHE</title>
    <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;600&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- intlTelInput CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <style>
        body {
            padding-top: 76px;
        }

        /* Navigation Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .nav-link {
            position: relative;
            padding: 8px 15px !important;
            margin: 0 5px;
            font-weight: 500;
            color: #333 !important;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: #0d6efd;
            transition: all 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .nav-link i {
            transition: transform 0.3s ease;
        }

        .nav-link:hover i {
            transform: translateY(-2px);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar .nav-item {
            animation: fadeInUp 0.6s ease backwards;
            animation-delay: calc(0.1s * var(--animation-order));
        }

        /* Dropdown Styles */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            padding: 0.6rem 1rem;
            border-radius: 0.3rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(13, 110, 253, 0.1);
            transform: translateX(5px);
        }

        /* Animation du dropdown */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate.slideIn {
            animation: slideIn 0.3s ease;
        }

        /* Sidebar Mobile */
        .offcanvas {
            border-radius: 0 20px 20px 0;
        }

        .offcanvas-header {
            padding: 20px;
            background: #f8f9fa;
        }

        .offcanvas .nav-link {
            padding: 12px 20px !important;
            font-size: 1.1rem;
        }

        /* Footer Styles */
        .footer {
            background-color: #1a1a1a !important;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer a {
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #0d6efd !important;
        }

        .social-links a {
            display: inline-block;
            width: 35px;
            height: 35px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: #0d6efd;
            transform: translateY(-3px);
        }

        /* Profile Specific Styles */
        .profile-sidebar {
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .profile-sidebar .nav-link {
            border-radius: 8px;
            margin: 5px 0;
        }

        .profile-sidebar .nav-link.active {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd !important;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
<?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($success_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Header Navigation -->
<header class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
         <a class="navbar-brand" href="../index.php">
            <span style="color: #04013F; font-weight: 700; font-size: 1.5rem;">WEMAN</span><span style="color: #0d6efd; font-weight: 700; font-size: 1.5rem;">TCHE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item" style="--animation-order: 1">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 2">
                    <a class="nav-link" href="documents.php">
                        <i class="fas fa-file-alt me-1"></i>Documents
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 3">
                    <a class="nav-link" href="services.php">
                        <i class="fas fa-cogs me-1"></i>Services
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 4">
                    <a class="nav-link" href="redaction.php">
                        <i class="fas fa-pen-fancy me-1"></i>Rédaction
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 5">
                    <a class="nav-link" href="apropos.php">
                        <i class="fas fa-info-circle me-1"></i>À propos
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Sidebar for mobile -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar">
    <div class="offcanvas-header">
        <a class="navbar-brand" href="../index.php">
            <span style="color: #04013F;">WEMAN</span><span style="color: #87CEEB;">TCHE</span>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-home me-2"></i>Accueil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="documents.php">
                    <i class="fas fa-file-alt me-2"></i>Documents
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services.php">
                    <i class="fas fa-cogs me-2"></i>Services
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="redaction.php">
                    <i class="fas fa-pen me-2"></i>Rédaction
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="apropos.php">
                    <i class="fas fa-info-circle me-2"></i>À propos
                </a>
            </li>
        </ul>
    </div>
</div>
  
    <!-- Contenu de la page Mon Compte -->
    <section class="profile-section py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="card profile-sidebar">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="profile-image mb-3">
                                    <span class="rounded-circle" style="font-size: 50px;">👤</span>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($user['role']); ?></p>
                            </div>
                            <div class="profile-menu">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#profile-info">
                                            <i class="fas fa-user me-2"></i>Informations personnelles
                                        </a>
                                    </li>
                                    <!--
                                    <li class="nav-item">
                                        <a class="nav-link" href="#documents">
                                            <i class="fas fa-file-alt me-2"></i>Mes documents
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#orders">
                                            <i class="fas fa-shopping-cart me-2"></i>Mes commandes
                                        </a>
                                    </li>
                                     -->
                                    <li class="nav-item">
                                        <a class="nav-link" href="#settings">
                                            <i class="fas fa-cog me-2"></i>Paramètres
                                        </a>
                                    </li>
                                   
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- Profile Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Informations personnelles</h4>
                            <?php if (isset($message)): ?>
                                <div class="alert alert-info">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                            <?php endif; ?>
                            <form id="profileForm" action="profile.php" method="POST">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Prénom</label>
                                        <input type="text" class="form-control" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nom</label>
                                        <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <div class="input-group">
                                        <select class="form-select" style="max-width: 150px;" name="indicatif" disabled>
                                            <option value="+229">Bénin (+229)</option>
                                            <option value="+225">Côte d'Ivoire (+225)</option>
                                            <option value="+221">Sénégal (+221)</option>
                                            <option value="+223">Mali (+223)</option>
                                            <option value="+226">Burkina Faso (+226)</option>
                                            <option value="+227">Niger (+227)</option>
                                            <option value="+228">Togo (+228)</option>
                                            <option value="+237">Cameroun (+237)</option>
                                            <option value="+241">Gabon (+241)</option>
                                        </select>
                                        <input type="tel" class="form-control" name="telephone" 
                                               value="<?php echo substr(htmlspecialchars($user['telephone']), 4); ?>" 
                                               required disabled>
                                    </div>
                                </div>
                                <div class="mb-3" id="passwordField" style="display: none;">
                                    <label class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <button type="button" id="editButton" class="btn btn-secondary">Modifier les informations</button>
                                <button type="submit" id="saveButton" class="btn btn-primary" disabled>Sauvegarder les modifications</button>
                            </form>
                        </div>
                    </div>
                    <!-- Paramètres -->
                    <div class="card mb-4" id="settings">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Paramètres</h4>
                            <div class="list-group">
                                <!-- Bouton de déconnexion -->
                                <form method="POST" action="profile.php" class="mb-3">
                                    <button type="submit" name="logout" class="list-group-item list-group-item-action d-flex align-items-center">
                                        <i class="fas fa-sign-out-alt text-warning me-3"></i>
                                        <span>Se déconnecter</span>
                                    </button>
                                </form>
                                <!-- Bouton de suppression de compte -->
                                <form method="POST" action="profile.php" class="mb-3">
                                    <button type="submit" name="delete_account" class="list-group-item list-group-item-action d-flex align-items-center text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
                                        <i class="fas fa-trash-alt text-danger me-3"></i>
                                        <span>Supprimer mon compte</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal Changement de mot de passe -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Changer le mot de passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="nouveau_mot_de_passe" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="change_password" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
     <!-- Footer -->
<footer class="footer bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4">
            <!-- À propos -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 fw-bold">À propos de WEMANTCHE</h5>
                <p class="text-light mb-3">
                    WEMANTCHE est votre plateforme de confiance pour tous vos besoins en documents académiques et professionnels. Notre mission est de fournir des services de qualité pour votre réussite.
                </p>
                <div class="social-links mt-4">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>

            <!-- Liens Rapides -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 fw-bold">Liens Rapides</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="documents.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Documents
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="services.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Services
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="redaction.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Rédaction sur mesure
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="apropos.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>À propos
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 fw-bold">Contactez-nous</h5>
                <ul class="list-unstyled">
                    <li class="mb-3 text-light">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Porto-Novo, Bénin
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:wemanwade@gmail.com" class="text-light text-decoration-none">wemanwade@gmail.com</a>
                    </li>
                    <li class="mb-3 text-light">
                        <i class="fas fa-phone me-2"></i>
                        +229 01-49-48-98-71
                    </li>
                    <li class="mb-3 text-light">
                        <i class="fas fa-clock me-2"></i>
                        Lun - Sam: 8h00 - 18h00
                    </li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row mt-4">
            <div class="col-12">
                <hr class="bg-light opacity-25">
                <p class="text-center text-light mb-0">
                    © <?php echo date('Y'); ?> WEMANTCHE. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- intlTelInput JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const profileForm = document.getElementById('profileForm');

        // Gestion du bouton Modifier
        editButton.addEventListener('click', function() {
            const formInputs = document.querySelectorAll('#profileForm input, #profileForm select');
            formInputs.forEach(input => input.disabled = false);
            document.getElementById('passwordField').style.display = 'block';
            saveButton.disabled = false;
        });

        // Gestion de la soumission du formulaire
        profileForm.addEventListener('submit', function(e) {
            const indicatif = document.querySelector('select[name="indicatif"]').value;
            const telephone = document.querySelector('input[name="telephone"]').value;
            document.querySelector('input[name="telephone"]').value = telephone.replace(/\D/g, '');
        });
    </script>
</body>
</html>