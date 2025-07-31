<?php
session_start();
require_once '../config/connexion.php';
require_once 'protect-source.php';

// Déplacer la vérification de session après le DOCTYPE pour permettre l'affichage de la notification
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <title>Services - WEMANTCHE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;600&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <style>
        body {
            padding-top: 76px;
        }

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

        .footer hr {
            margin: 2rem 0;
        }

        .footer .fa-chevron-right {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .footer a:hover .fa-chevron-right {
            transform: translateX(3px);
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

        /* Services Cards */
        .card {
            border: none;
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 250px; /* Augmentation de la hauteur */
            object-fit: cover;
            width: 100%;
        }

        .card-body {
            padding: 2.5rem; /* Augmentation du padding */
        }

        .card-title {
            color: #2d3436;
            font-weight: 600;
            margin-bottom: 1.5rem; /* Ajout d'une marge */
        }

        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #0099ff);
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }

        /* Services Header */
        .services-header {
            background: linear-gradient(135deg, #f6f9fe 0%, #f1f4f9 100%);
            padding: 80px 0;
            margin-bottom: 40px;
        }

        .services-header h1 {
            font-size: 2.5rem;
            color: #2d3436;
            margin-bottom: 20px;
        }

        .services-header .lead {
            color: #636e72;
            font-size: 1.2rem;
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

        /* Liste à puces personnalisée */
        .list-unstyled li {
            padding: 8px 0;
        }

        .fa-check {
            background: rgba(13, 110, 253, 0.1);
            padding: 5px;
            border-radius: 50%;
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .services-header {
                padding: 40px 0;
            }
            
            .services-header h1 {
                font-size: 2rem;
            }
            
            .card {
                margin-bottom: 20px;
            }
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
                    <a class="nav-link active" href="services.php">
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
                           
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
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
    <a class="navbar-brand" href="#">
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
            <li class="nav-item"><a class="nav-link" href="redaction.php">Rédaction sur mesure</a></li>
           
            <li class="nav-item"><a class="nav-link" href="apropos.php">À propos</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Mon Compte</a></li>
        </ul>
    </div>
</div>
    <!-- Services Header -->
    <section class="services-header bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="fw-bold mb-4">Nos Services</h1>
                    <p class="lead">Découvrez l'ensemble de nos services professionnels</p>
                </div>
                <div class="col-lg-6">
                    <!-- Image ou illustration -->
                    <img src="../assets/images/services1.jpg" alt="Services" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Details -->
    <section class="services-details py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Rédaction sur mesure -->
                <div class="col-lg-3">
                    <div class="card h-100 shadow">
                        <img src="../assets/images/services2.jpg" class="card-img-top" alt="Rédaction">
                        <div class="card-body">
                            <h3 class="card-title mb-3">Rédaction sur mesure</h3>
                            <p class="card-text">Un service personnalisé pour vos besoins spécifiques en rédaction.</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Rédaction de mémoires et rapports</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Correction et relecture</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Accompagnement personnalisé</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Respect des délais</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center pb-4">
                            <a href="redaction.php" class="btn btn-primary">En savoir plus</a>
                        </div>
                    </div>
                </div>

                <!-- Formation -->
                <div class="col-lg-3">
                    <div class="card h-100 shadow">
                        <img src="../assets/images/services4.jpg" class="card-img-top" alt="Formation">
                        <div class="card-body">
                            <h3 class="card-title mb-3">Formation</h3>
                            <p class="card-text">Développez vos compétences avec nos formations spécialisées.</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Techniques de rédaction</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Méthodologie de recherche</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Ateliers pratiques</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Certification professionnelle</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center pb-4">
                            <a href="mailto:towemanwade@gmail.com" class="btn btn-primary">Contactez-nous</a>
                        </div>
                    </div>
                </div>

                <!-- Consultation -->
                <div class="col-lg-3">
                    <div class="card h-100 shadow">
                        <img src="../assets/images/services5.jpg" class="card-img-top" alt="Consultation">
                        <div class="card-body">
                            <h3 class="card-title mb-3">Consultation</h3>
                            <p class="card-text">Des experts à votre écoute pour vous guider dans vos projets.</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Conseil personnalisé</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Orientation académique</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Suivi de projet</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Support continu</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center pb-4">
                            <a href="mailto:towemanwade@gmail.com" class="btn btn-primary">Contactez-nous</a>
                        </div>
                    </div>
                </div>

                <!-- Bibliothèque -->
                <div class="col-lg-3">
                    <div class="card h-100 shadow">
                        <img src="../assets/images/services7.jpg" class="card-img-top" alt="Bibliothèque">
                        <div class="card-body">
                            <h3 class="card-title mb-3">Bibliothèque</h3>
                            <p class="card-text">Accédez à notre collection complète de documents académiques et professionnels.</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Documents variés</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Ressources académiques</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Mise à jour régulière</li>
                                <li class="mb-2"><i class="fas fa-check text-primary me-2"></i>Accès simplifié</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center pb-4">
                            <a href="#" class="btn btn-primary">Découvrir</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Commented out Pricing Section
    <div class="pricing py-5">
        <h2 class="text-center mb-5">Nos Tarifs</h2>
        <div class="row g-4">
Basic 
            <div class="col-lg-4">
                <div class="card pricing-card h-100">
                    <div class="card-body text-center p-5">
                        <h3 class="card-title">Basic</h3>
                        <div class="price my-4">
                            <span class="h2">5000</span>
                            <span class="h4">FCFA</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Accès aux documents de base</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>3 téléchargements/mois</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Support standard</li>
                        </ul>
                        <a href="#" class="btn btn-outline-primary">Choisir ce plan</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card pricing-card h-100 border-primary">
                    <div class="card-body text-center p-5">
                        <h3 class="card-title">Premium</h3>
                        <div class="price my-4">
                            <span class="h2">15000</span>
                            <span class="h4">FCFA</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Accès illimité aux documents</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Téléchargements illimités</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Support prioritaire</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Consultation gratuite</li>
                        </ul>
                        <a href="#" class="btn btn-primary">Choisir ce plan</a>
                    </div>
                </div>
            </div>

           
            <div class="col-lg-4">
                <div class="card pricing-card h-100">
                    <div class="card-body text-center p-5">
                        <h3 class="card-title">Sur Mesure</h3>
                        <div class="price my-4">
                            <span class="h4">Contactez-nous</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Services personnalisés</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Rédaction sur mesure</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Accompagnement dédié</li>
                            <li class="mb-2"><i class="fas fa-check me-2"></i>Formation privée</li>
                        </ul>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#contactModal">
                            Nous contacter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    -->

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
                        <a href="pages/documents.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Documents
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="pages/services.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Services
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="pages/redaction.php" class="text-light text-decoration-none">
                            <i class="fas fa-chevron-right me-2"></i>Rédaction sur mesure
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="pages/apropos.php" class="text-light text-decoration-none">
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
                        +229 01 49 48 98 71
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
    <!-- Custom JS -->
    <script src="../assets/js/main.js"></script>
</body>
</html>