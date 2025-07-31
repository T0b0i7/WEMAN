<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/connexion.php';

// Remplacer ces lignes
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        // Validation des données
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("Vous devez être connecté pour envoyer une demande");
        }

        // Validation des champs requis
        $required = ['categorie_id', 'sujet_theme', 'description', 'delai_souhaite'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ $field est requis");
            }
        }

        // Validation de la date
        $delai_souhaite = $_POST['delai_souhaite'];
        $today = new DateTime();
        $delai_date = new DateTime($delai_souhaite);
        
        if ($delai_date <= $today) {
            throw new Exception("La date doit être postérieure à aujourd'hui");
        }

        // Calcul du budget
        $categorie_id = filter_var($_POST['categorie_id'], FILTER_VALIDATE_INT);
        $stmt_categorie = $pdo->prepare("SELECT prix_standard, prix_urgent, delai_standard_jours FROM categories_prix WHERE categorie_id = ?");
        $stmt_categorie->execute([$categorie_id]);
        $categorie = $stmt_categorie->fetch(PDO::FETCH_ASSOC);

        if (!$categorie) {
            throw new Exception("Catégorie non trouvée");
        }

        // Déterminer si c'est un prix urgent
        $today = new DateTime();
        $delai_date = new DateTime($delai_souhaite);
        $interval = $today->diff($delai_date);
        $jours_restants = $interval->days;

        $budget = $_POST['budget'] ?? 0; // Si budget vide, mettre 0
        $budget = filter_var($budget, FILTER_VALIDATE_FLOAT);
        
        if ($budget === false) {
            $budget = 0;
        }

        // Si budget est 0, utiliser le prix standard ou urgent selon le délai
        if ($budget == 0) {
            if ($jours_restants <= $categorie['delai_standard_jours']) {
                $budget = $categorie['prix_urgent'] ?? $categorie['prix_standard'] * 1.5;
            } else {
                $budget = $categorie['prix_standard'];
            }
        }

        // Récupérer les noms de classe et filière si nécessaire
        $classe_nom = null;
        $filiere_nom = null;

        if (!empty($_POST['classe_id'])) {
            $stmt_classe = $pdo->prepare("SELECT nom FROM classes WHERE id = ?");
            $stmt_classe->execute([$_POST['classe_id']]);
            $classe = $stmt_classe->fetch(PDO::FETCH_ASSOC);
            $classe_nom = $classe['nom'] ?? null;
        }

        if (!empty($_POST['filiere_id'])) {
            $stmt_filiere = $pdo->prepare("SELECT nom FROM filieres WHERE id = ?");
            $stmt_filiere->execute([$_POST['filiere_id']]);
            $filiere = $stmt_filiere->fetch(PDO::FETCH_ASSOC);
            $filiere_nom = $filiere['nom'] ?? null;
        }

        // Insertion dans la base de données
        $sql = "INSERT INTO demandes_redaction (
            utilisateur_id, categorie_id, sujet_theme, filiere, filiere_id, 
            classe, classe_id, matiere, objectifs, plan_souhaite, 
            consignes_specifiques, description, delai_souhaite, 
            budget, statut
        ) VALUES (
            :utilisateur_id, :categorie_id, :sujet_theme, :filiere, :filiere_id,
            :classe, :classe_id, :matiere, :objectifs, :plan_souhaite,
            :consignes_specifiques, :description, :delai_souhaite,
            :budget, 'en_attente'
        )";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'utilisateur_id' => $_SESSION['user_id'],
            'categorie_id' => $categorie_id,
            'sujet_theme' => strip_tags($_POST['sujet_theme']),
            'filiere' => $filiere_nom,
            'filiere_id' => !empty($_POST['filiere_id']) ? filter_var($_POST['filiere_id'], FILTER_VALIDATE_INT) : null,
            'classe' => $classe_nom,
            'classe_id' => !empty($_POST['classe_id']) ? filter_var($_POST['classe_id'], FILTER_VALIDATE_INT) : null,
            'matiere' => !empty($_POST['matiere']) ? strip_tags($_POST['matiere']) : null,
            'objectifs' => !empty($_POST['objectifs']) ? strip_tags($_POST['objectifs']) : null,
            'plan_souhaite' => !empty($_POST['plan_souhaite']) ? strip_tags($_POST['plan_souhaite']) : null,
            'consignes_specifiques' => !empty($_POST['consignes_specifiques']) ? strip_tags($_POST['consignes_specifiques']) : null,
            'description' => strip_tags($_POST['description']),
            'delai_souhaite' => $delai_souhaite,
            'budget' => $budget
        ]);

        if (!$result) {
            throw new Exception("Erreur lors de l'enregistrement de la demande");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Votre demande a été envoyée avec succès!',
            'redirect' => 'redaction.php?success=1'
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Récupérer les catégories avec leurs prix
try {
    $sqlCategories = "
        SELECT DISTINCT
            cd.id,
            cd.nom,
            cd.description,
            cp.prix_standard,
            cp.prix_urgent,
            cp.seuil_mots,
            cp.prix_par_mot_supp,
            cp.delai_standard_jours,
            cp.delai_urgent_jours
        FROM categories_documents cd
        LEFT JOIN categories_prix cp ON cd.id = cp.categorie_id
        WHERE cd.active = 1
        ORDER BY cd.nom ASC
    ";
    
    $stmtCategories = $pdo->prepare($sqlCategories);
    $stmtCategories->execute();
    $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($categories)) {
        throw new Exception("Aucune catégorie de document n'est disponible pour le moment.");
    }

} catch (PDOException $e) {
    error_log("Erreur DB: " . $e->getMessage());
    die("Une erreur est survenue lors de la récupération des catégories. Veuillez réessayer plus tard.");
}

// Configuration des prix par catégorie
$categoriesPrix = [];
$last_categorie = end($categories);
foreach ($categories as $categorie) {
    $categoriesPrix[$categorie['id']] = [
        'prix_standard' => $categorie['prix_standard'],
        'prix_urgent' => $categorie['prix_urgent'],
        'delai_urgent_jours' => $categorie['delai_urgent_jours']
    ];
}

// Récupérer les classes
$sqlClasses = "SELECT id, nom, niveau FROM classes WHERE actif = 1 ORDER BY niveau, ordre";
$stmtClasses = $pdo->prepare($sqlClasses);
$stmtClasses->execute();
$classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les filières
$sqlFilieres = "SELECT id, nom, niveau, departement FROM filieres WHERE actif = 1 ORDER BY niveau, departement, nom";
$stmtFilieres = $pdo->prepare($sqlFilieres);
$stmtFilieres->execute();
$filieres = $stmtFilieres->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les matières
$stmt_matieres = $pdo->query("SELECT id, nom FROM matieres ORDER BY nom ASC");
$matieres = $stmt_matieres->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <title>Rédaction sur mesure - WEMANTCHE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .swal2-popup {
            font-size: 1rem;
        }
        .swal2-title {
            font-size: 1.5rem;
        }
        .swal2-confirm {
            background-color: #0d6efd !important;
        }
        .swal2-styled.swal2-confirm:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.5) !important;
        }
        .budget-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .urgent-price {
            color: #dc3545;
            font-weight: bold;
        }
        .hidden-field {
            display: none;
        }
        @keyframes successAnimation {
            0% { transform: scale(0.7); opacity: 0; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-animation {
            animation: successAnimation 0.5s ease-in-out;
        } /* Navigation Styles */
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

        /* Animation */
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

        /* Responsive */
        @media (max-width: 991px) {
            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .nav-link {
                padding: 10px 15px !important;
            }
        }

        /* Sidebar Mobile */
        .offcanvas {
            background-color: #fff;
            width: 280px;
            border-radius: 0 20px 20px 0;
        }

        .offcanvas-header {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 0 20px 0 0;
        }

        .offcanvas .nav-link {
            padding: 12px 20px !important;
            font-size: 1.1rem;
            border-radius: 10px;
            margin: 5px 15px;
        }

        .offcanvas .nav-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }#pricingInfo .card {
        transition: transform 0.2s;
        border: none;
        background-color: #fff;
    }

    #pricingInfo .card:hover {
        transform: translateY(-5px);
    }

    #pricingInfo .card-title {
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    #pricingInfo .text-primary {
        color: #0d6efd !important;
    }

    #pricingInfo .text-danger {
        color: #dc3545 !important;
    }

    #pricingInfo .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    }

    #pricingInfo .card {
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    #pricingInfo .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    #pricingInfo .card-title {
        color: #333;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    #pricingInfo .pricing-details li {
        margin-bottom: 0.8rem;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    #pricingInfo .pricing-details strong {
        color: #0d6efd;
    }

    #pricingInfo .card:last-child .pricing-details strong {
        color: #dc3545;
    }

    .animate__animated {
        animation-duration: 0.5s;
    }

    #pricingInfo .fas {
        width: 20px;
        text-align: center;
        margin-right: 8px;
    }
    </style>
</head>
<body>
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
                    <a class="nav-link active" href="redaction.php">
                        <i class="fas fa-pen-fancy me-1"></i>Rédaction
                    </a>
                </li>
                <li class="nav-item" style="--animation-order: 5">
                    <a class="nav-link" href="apropos.php">
                        <i class="fas fa-info-circle me-1"></i>À propos
                    </a>
                </li>
            </ul>
            <div class="d-none d-lg-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary rounded-pill dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>Mon Compte
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2 text-primary"></i>Mon Profil
                                </a>
                            </li>
                            
                            <li>
                                <a class="dropdown-item text-danger" href="../logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary rounded-pill me-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                    <a href="register.php" class="btn btn-primary rounded-pill">
                        <i class="fas fa-user-plus me-2"></i>Inscription
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Sidebar for mobile -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
    <a class="navbar-brand" href="../index.php">
            <span class="ms-2">
                <span style="color: #04013F;">WEMAN</span><span style="color: #87CEEB;">TCHE</span>
            </span>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
    <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
            <li class="nav-item">
                <!-- Lien direct simplifié -->
                <a class="nav-link" href="documents.php">Documents</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="redaction.php">Rédaction</a></li>
           
            <li class="nav-item"><a class="nav-link" href="apropos.php">À propos</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Mon Compte</a></li>
        </ul>
    </div>
</div>
    <!-- Header Section avec plus d'espacement -->
    <section class="redaction-header bg-light py-5 mt-5">
        <div class="container pt-4">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="fw-bold mb-4">Rédaction sur mesure</h1>
                    <p class="lead mb-4">Confiez-nous vos projets de rédaction et obtenez des documents professionnels adaptés à vos besoins.</p>
                    <div class="features mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-primary me-3"></i>
                            <span>Équipe de rédacteurs professionnels</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-primary me-3"></i>
                            <span>Respect des délais garantis</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-primary me-3"></i>
                            <span>Satisfaction client assurée</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="../assets/images/redaction-hero.png" alt="Rédaction" class="img-fluid">
                </div>
            </div>
        </div>
    </section>


<!-- Demande Form Section -->
<section class="demande-form py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Faire une demande de rédaction sur mesure</h2>
        
       
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <form id="demandeForm" method="POST" action="" class="needs-validation" novalidate>
                            <!-- Catégorie de document -->
                            <div class="mb-3">
                                <label class="form-label">Catégorie de document *</label>
                                <select name="categorie_id" id="categorieSelect" class="form-select" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?= intval($categorie['id']) ?>" 
                                            data-prix-standard="<?= floatval($categorie['prix_standard']) ?? 0 ?>"
                                            data-prix-urgent="<?= floatval($categorie['prix_urgent']) ?? 0 ?>"
                                            data-delai-standard="<?= intval($categorie['delai_standard_jours']) ?? 0 ?>"
                                            data-delai-urgent-jours="<?= intval($categorie['delai_urgent_jours']) ?? 0 ?>"
                                            data-seuil-mots="<?= intval($categorie['seuil_mots']) ?? 0 ?>"
                                            data-prix-par-mot-supp="<?= floatval($categorie['prix_par_mot_supp']) ?? 0 ?>">
                                            <?= htmlspecialchars($categorie['nom'] ?? '') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner une catégorie</div>
                            </div>

                            <!-- Champs spécifiques Exposé -->
                            <div id="exposeFields" class="hidden-field">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Classe *</label>
                                            <select name="classe_id" id="classeSelect" class="form-select" required>
                                                <option value="">Sélectionner une classe</option>
                                                <?php foreach ($classes as $classe): ?>
                                                    <option value="<?= htmlspecialchars($classe['id']) ?>">
                                                        <?= htmlspecialchars($classe['nom']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Veuillez sélectionner une classe</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Matière *</label>
                                            <select name="matiere" id="matiereSelect" class="form-select" required>
                                                <option value="">Sélectionner une matière</option>
                                                <?php foreach ($matieres as $matiere): ?>
                                                    <option value="<?= htmlspecialchars($matiere['nom']) ?>">
                                                        <?= htmlspecialchars($matiere['nom']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Veuillez sélectionner une matière</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Champs spécifiques Mémoire -->
                            <div id="memoireFields" class="hidden-field">
                                <div class="mb-3">
                                    <label class="form-label">Filière *</label>
                                    <select name="filiere_id" id="filiereSelect" class="form-select" required>
                                        <option value="">Sélectionner une filière</option>
                                        <?php foreach ($filieres as $filiere): ?>
                                            <option value="<?= htmlspecialchars($filiere['id']) ?>">
                                                <?= htmlspecialchars($filiere['nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Veuillez sélectionner une filière</div>
                                </div>
                            </div>

                            <!-- Champs communs -->
                            <div class="mb-3">
                                <label class="form-label">Sujet/Thème *</label>
                                <input type="text" name="sujet_theme" class="form-control" required>
                                <div class="invalid-feedback">Ce champ est requis</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Objectifs</label>
                                <textarea name="objectifs" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Plan souhaité</label>
                                <textarea name="plan_souhaite" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Consignes spécifiques</label>
                                <textarea name="consignes_specifiques" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description générale *</label>
                                <textarea name="description" class="form-control" rows="5" required></textarea>
                                <div class="invalid-feedback">Ce champ est requis</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Délai souhaité *</label>
                                    <input type="date" name="delai_souhaite" id="delaiSouhaite" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                    <div class="invalid-feedback">Veuillez sélectionner une date valide</div>
                                    <div id="delaiInfo" class="budget-info mt-1"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Budget (FCFA)</label>
                                    <input type="number" name="budget" id="budgetInput" class="form-control" value="0">
                                    <div id="budgetInfo" class="budget-info mt-1"></div>
                                </div>
                            </div>

                            <div id="pricingInfo" class="mt-4" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card shadow-sm mb-3">
                                            <div class="card-body">
                                                <h5 class="card-title"><i class="fas fa-clock text-primary"></i> Forfait Standard</h5>
                                                <ul class="list-unstyled pricing-details">
                                                    <li><i class="fas fa-check-circle text-primary me-2"></i>Prix: <span id="prixStandardResume"></span></li>
                                                    <li><i class="fas fa-file-alt text-primary me-2"></i>Seuil de mots: <span id="seuilMotsResume"></span></li>
                                                    <li><i class="fas fa-plus-circle text-primary me-2"></i>Prix par mot supplémentaire: <span id="prixMotResume"></span></li>
                                                    <li><i class="fas fa-calendar-alt text-primary me-2"></i>Délai standard: <span id="delaiStandardResume"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card shadow-sm mb-3">
                                            <div class="card-body">
                                                <h5 class="card-title"><i class="fas fa-bolt text-danger"></i> Forfait Express</h5>
                                                <ul class="list-unstyled pricing-details">
                                                    <li><i class="fas fa-check-circle text-danger me-2"></i>Prix: <span id="prixUrgentResume"></span></li>
                                                    <li><i class="fas fa-file-alt text-danger me-2"></i>Seuil de mots: <span id="seuilMotsUrgentResume"></span></li>
                                                    <li><i class="fas fa-plus-circle text-danger me-2"></i>Prix par mot supplémentaire: <span id="prixMotUrgentResume"></span></li>
                                                    <li><i class="fas fa-calendar-alt text-danger me-2"></i>Délai express: <span id="delaiUrgentResume"></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer la demande
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('.form-select').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    const form = $('#demandeForm');
    const categorieSelect = $('#categorieSelect');
    const budgetInput = $('#budgetInput');
    const delaiSouhaite = $('#delaiSouhaite');
    const exposeFields = $('#exposeFields');
    const memoireFields = $('#memoireFields');
    const budgetInfo = $('#budgetInfo');
    const delaiInfo = $('#delaiInfo');

    // Fonction pour afficher/masquer les champs spécifiques
    function toggleSpecificFields() {
        const selectedOption = categorieSelect.find('option:selected');
        const categorieNom = selectedOption.text().toLowerCase();
        
        // Masquer tous les champs spécifiques
        exposeFields.addClass('hidden-field').find('select').removeAttr('required');
        memoireFields.addClass('hidden-field').find('select').removeAttr('required');
        
        // Afficher les champs appropriés
        if (categorieNom.includes('exposé') || categorieNom.includes('expose')) {
            exposeFields.removeClass('hidden-field').find('select').attr('required', 'required');
        } else if (categorieNom.includes('mémoire') || categorieNom.includes('memoire')) {
            memoireFields.removeClass('hidden-field').find('select').attr('required', 'required');
        }
    }

    // Mettre à jour les champs en fonction de la catégorie sélectionnée
    categorieSelect.on('change', function() {
        toggleSpecificFields();
        updateBudget();
        updatePricingInfo();
    });

    // Mettre à jour le budget quand la date ou la catégorie change
    delaiSouhaite.on('change', updateBudget);

    function updateBudget() {
        const selectedOption = categorieSelect.find('option:selected');
        const prixStandard = selectedOption.data('prix-standard');
        const prixUrgent = selectedOption.data('prix-urgent');
        const delaiStandard = selectedOption.data('delai-standard');
        const delaiValue = delaiSouhaite.val();
        
        if (!prixStandard || !delaiValue) {
            budgetInput.val(0);
            budgetInfo.html('');
            return;
        }
        
        // Calculer les jours restants
        const today = new Date();
        const delaiDate = new Date(delaiValue);
        const diffTime = delaiDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Déterminer si c'est un délai urgent
        let isUrgent = false;
        let nouveauBudget = prixStandard;
        
        if (diffDays <= delaiStandard) {
            isUrgent = true;
            nouveauBudget = prixUrgent || Math.round(prixStandard * 1.5); // Si pas de prix urgent, majorer de 50%
        }
        
        // Mettre à jour l'affichage
        if (isUrgent) {
            budgetInfo.html(`Prix urgent: <span class="urgent-price">${nouveauBudget.toLocaleString()} FCFA</span> (délai court)`);
            delaiInfo.html(`Délai très court (${diffDays} jours)`);
        } else {
            budgetInfo.html(`Prix standard: ${nouveauBudget.toLocaleString()} FCFA`);
            delaiInfo.html(`Délai normal (${diffDays} jours)`);
        }
        
        // Si le budget est à 0 ou vide, mettre à jour avec le prix calculé
        if (parseFloat(budgetInput.val()) <= 0 || budgetInput.val() === '') {
            budgetInput.val(nouveauBudget);
        }
    }

    function showPricingInfo() {
        const categorieId = $('#categorieSelect').val();
        const classeId = $('#classeSelect').val();
        const matiereId = $('#matiereSelect').val();
        const selectedCategorie = categorieSelect.find('option:selected').text().toLowerCase();

        // Cacher le bloc de prix par défaut
        $('#pricingInfo').hide();

        // Vérifier si c'est un exposé
        if (selectedCategorie.includes('exposé') || selectedCategorie.includes('expose')) {
            // Vérifier si tous les champs requis sont remplis
            if (categorieId && classeId && matiereId) {
                updatePricingInfo();
                $('#pricingInfo').slideDown(300);
            }
        } else {
            // Pour les autres types de documents, afficher directement après la sélection de la catégorie
            if (categorieId) {
                updatePricingInfo();
                $('#pricingInfo').slideDown(300);
            }
        }
    }

    function updatePricingInfo() {
        const selectedOption = categorieSelect.find('option:selected');
        const categorieId = selectedOption.val();
        
        if (!categorieId) {
            $('#pricingInfo').hide();
            return;
        }

        // Récupérer les données
        const prixStandard = parseFloat(selectedOption.data('prix-standard')) || 0;
        const prixUrgent = parseFloat(selectedOption.data('prix-urgent')) || 0;
        const delaiStandard = parseInt(selectedOption.data('delai-standard')) || 0;
        const delaiUrgent = parseInt(selectedOption.data('delai-urgent-jours')) || 0;
        const seuilMots = parseInt(selectedOption.data('seuil-mots')) || 0;
        const prixParMot = parseFloat(selectedOption.data('prix-par-mot-supp')) || 0;

        // Animation de mise à jour des prix
        $('#pricingInfo .card').addClass('animate__animated animate__fadeIn');

        // Mettre à jour les informations avec animation
        const elements = {
            'prixStandardResume': `${prixStandard.toLocaleString()} FCFA`,
            'prixUrgentResume': `${prixUrgent.toLocaleString()} FCFA`,
            'seuilMotsResume': `${seuilMots} mots`,
            'seuilMotsUrgentResume': `${seuilMots} mots`,
            'prixMotResume': `${prixParMot.toLocaleString()} FCFA/mot`,
            'prixMotUrgentResume': `${(prixParMot * 1.5).toLocaleString()} FCFA/mot`,
            'delaiStandardResume': `${delaiStandard} jours`,
            'delaiUrgentResume': `${delaiUrgent} jours`
        };

        Object.entries(elements).forEach(([id, value]) => {
            $(`#${id}`).fadeOut(200, function() {
                $(this).html(`<strong>${value}</strong>`).fadeIn(200);
            });
        });
    }

    // Modifier les événements de changement
    $('#categorieSelect, #classeSelect, #matiereSelect').on('change', function() {
        showPricingInfo();
    });

    // Gestion de la soumission du formulaire
    form.on('submit', function(e) {
        e.preventDefault();
        
        // Validation côté client
        if (this.checkValidity() === false) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }
        
        // Envoyer le formulaire via AJAX
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Envoi en cours...',
            html: 'Veuillez patienter',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: '<strong>Demande envoyée avec succès !</strong>',
                        icon: 'success',
                        html: `
                            <div class="animate__animated animate__fadeIn">
                                <div class="mb-3">
                                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                                    <p class="mb-2">Votre demande a été enregistrée avec succès.</p>
                                </div>
                                <div class="alert alert-info">
                                    <h6 class="mb-2"><i class="fas fa-info-circle"></i> Prochaines étapes :</h6>
                                    <ol class="text-start small">
                                        <li>Notre équipe va examiner votre demande</li>
                                        <li>Vous recevrez une confirmation par email</li>
                                        <li>Un rédacteur sera assigné à votre projet</li>
                                        <li>Vous pourrez suivre l'avancement dans votre espace personnel</li>
                                    </ol>
                                </div>
                                <div class="mt-3 text-muted">
                                    <small><i class="fas fa-clock"></i> Délai de traitement estimé : 24-48h</small>
                                </div>
                            </div>
                        `,
                        showCloseButton: true,
                        showConfirmButton: true,
                        confirmButtonText: '<i class="fas fa-user"></i> Voir mon espace personnel',
                        confirmButtonColor: '#0d6efd',
                        footer: '<a href="#contact" onclick="scrollToContact()">Besoin d\'aide ? Contactez-nous</a>',
                        customClass: {
                            container: 'custom-swal-container',
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        timer: 8000,
                        timerProgressBar: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'profile.php';
                        } else {
                            window.location.href = response.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: response.message || 'Une erreur est survenue',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de l\'envoi',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    // Initialiser la date minimale
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    delaiSouhaite.attr('min', tomorrow.toISOString().split('T')[0]);
});

// Fonction pour vérifier le statut des demandes
function checkDemandeStatus() {
    $.ajax({
        url: 'check_demande_status.php',
        type: 'POST',
        data: { user_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?> },
        success: function(response) {
            if (response.notifications && response.notifications.length > 0) {
                response.notifications.forEach(notification => {
                    const statusMessages = {
                        'en_cours': {
                            icon: 'info',
                            title: 'Demande en traitement',
                            text: 'Votre demande est en cours de traitement.',
                            color: '#0d6efd'
                        },
                        'termine': {
                            icon: 'success',
                            title: 'Demande terminée',
                            text: 'Votre demande a été traitée avec succès!',
                            color: '#198754'
                        },
                        'annule': {
                            icon: 'warning',
                            title: 'Demande annulée',
                            text: 'Votre demande a été annulée.',
                            color: '#dc3545'
                        },
                        'rejetee': {
                            icon: 'error',
                            title: 'Demande rejetée',
                            text: 'Votre demande a été rejetée.',
                            color: '#dc3545'
                        },
                        'validee': {
                            icon: 'success',
                            title: 'Demande validée',
                            text: 'Votre demande a été validée et sera traitée prochainement.',
                            color: '#198754'
                        }
                    };

                    const status = statusMessages[notification.statut] || {
                        icon: 'info',
                        title: 'Mise à jour',
                        text: 'Le statut de votre demande a changé.',
                        color: '#0d6efd'
                    };

                    Swal.fire({
                        icon: status.icon,
                        title: status.title,
                        html: `
                            <div class="text-start">
                                <p>${status.text}</p>
                                <hr>
                                <p><strong>Demande :</strong> ${notification.sujet_theme}</p>
                                <p><strong>Date de mise à jour :</strong> ${notification.date_modification}</p>
                                ${notification.message ? `<p><strong>Message :</strong> ${notification.message}</p>` : ''}
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        confirmButtonColor: status.color,
                        showCancelButton: false,
                        customClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Recharger la même page avec un paramètre de succès
                            window.location.href = 'redaction.php?status_update=1';
                        }
                    });
                });
            }
        }
    });
}

// Vérifier les statuts toutes les 30 secondes
setInterval(checkDemandeStatus, 30000);

// Vérifier immédiatement au chargement de la page
$(document).ready(function() {
    checkDemandeStatus();
});

// Fonction pour faire défiler vers la section de contact
function scrollToContact() {
    Swal.close(); // Ferme la boîte de dialogue
    const contactSection = document.querySelector('#contact');
    if (contactSection) {
        contactSection.scrollIntoView({ behavior: 'smooth' });
    } else {
        window.location.href = '../index.php#contact';
    }
}
</script>
</body>
</html>