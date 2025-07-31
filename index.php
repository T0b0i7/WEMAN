<!-- filepath: /C:/xampp/htdocs/WEMANTCHE/index.php -->
<?php
require_once 'config/connexion.php';

session_start();
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Administrateur';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="assets/images/WEMANTCHE LOGO p 2.png">
    <title>WEMANTCHE - Documents Académiques & Professionnels</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;600&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
    /* Icône flottante */
    .floating-icon {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        background-color: #007bff; /* Couleur de fond */
        color: #fff; /* Couleur de l'icône */
        border-radius: 50%; /* Cercle parfait */
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        z-index: 1000;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .floating-icon:hover {
        transform: scale(1.1); /* Agrandir légèrement au survol */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* Ombre plus prononcée */
    }

    .floating-icon i {
        font-size: 1.5rem; /* Taille de l'icône */
    }
    body {
        background: url('assets/images/coffee-mug-laptop-with-stationeries-wooden-desk-library.jpg') no-repeat center center fixed;
        background-size: cover;
        padding-top: 76px;
    }

    /* Styles pour les sections avec fond semi-transparent */
    .hero, .services, .contact {
        background-color: rgba(255, 255, 255, 0.75); /* Blanc avec 75% d'opacité */
        padding: 4rem 2rem; /* Espacement interne */
        border-radius: 10px; /* Coins arrondis */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Ombre légère */
        margin-bottom: 2rem; /* Espacement entre les sections */
    }

    /* Optionnel : Ajouter un effet de flou au fond */
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        filter: blur(2px); /* Effet de flou */
        z-index: -1; /* Envoyer à l'arrière-plan */
    }
    
    .admin-icon {
        color: #dc3545 !important; /* Rouge */
        transition: transform 0.3s ease;
    }
    
    .admin-icon:hover {
        transform: scale(1.1);
    }
    
    .admin-floating {
        position: fixed;
        bottom: 90px; /* Au-dessus de l'autre icône flottante */
        right: 20px;
        width: 60px;
        height: 60px;
        background-color: #dc3545;
        color: #fff;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        z-index: 1000;
        transition: transform 0.3s ease;
    }
    
    .admin-floating:hover {
        transform: scale(1.1);
    }

    /* Styles pour le texte hero */
    .hero-text {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 1s ease forwards;
    }

    .hero h1 {
        font-size: clamp(2rem, 5vw, 3.5rem);
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1rem;
    }

    .hero p.lead {
        font-size: clamp(1rem, 3vw, 1.5rem);
        font-weight: 600;
        opacity: 0.9;
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

    /* Media queries pour les différents écrans */
    @media (max-width: 768px) {
        .hero {
            padding: 2rem 1rem;
        }
        
        .hero h1 {
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .hero p.lead {
            text-align: center;
            margin-bottom: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .hero h1 {
            font-size: 1.8rem;
        }
        
        .hero p.lead {
            font-size: 1rem;
        }
    }

    /* Ajoutez ces styles dans la section style existante */
    .hero-slider {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        overflow: hidden;
    }

    .swiper {
        width: 100%;
        height: 100%;
    }

    .swiper-slide {
        text-align: center;
        background: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .hero-image {
        width: 100%;
        height: 100%;
    }

    .hero-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    /* Style pour la pagination */
    .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
        background: rgba(255, 255, 255, 0.8);
        opacity: 0.5;
    }

    .swiper-pagination-bullet-active {
        background: #fff;
        opacity: 1;
    }

    .footer-links a:hover {
        color: #fff !important;
        transition: all 0.3s ease;
    }

    .social-links a {
        display: inline-block;
        width: 35px;
        height: 35px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        text-align: center;
        line-height: 35px;
        transition: all 0.3s ease;
    }

    .social-links a:hover {
        background: #0d6efd;
        transform: translateY(-3px);
    }

    .footer hr {
        opacity: 0.1;
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

    .btn-outline-primary {
        border-width: 2px;
        font-weight: 500;
        padding: 8px 20px;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.2);
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

    /* Styles pour le dropdown */
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

    /* Styles pour le menu mobile */
    .offcanvas-account {
        background-color: rgba(248, 249, 250, 0.5);
        border-radius: 0.5rem;
    }

    .offcanvas-account .nav-link {
        padding: 0.5rem 1rem;
        border-radius: 0.3rem;
        transition: all 0.3s ease;
    }

    .offcanvas-account .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.1);
        transform: translateX(5px);
    }

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

    .auth-floating {
        position: fixed;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        z-index: 1000;
        transition: transform 0.3s ease;
    }

    .auth-floating:hover {
        transform: scale(1.1);
    }

    .login-floating {
        bottom: 160px;
        background-color: #0d6efd;
        color: #fff;
    }

    .register-floating {
        bottom: 90px;
        background-color: #198754;
        color: #fff;
    }
    </style>
</head>
<body>
<!-- Header Navigation -->
<header class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <span style="color: #04013F; font-weight: 700; font-size: 1.5rem;">WEMAN</span><span style="color: #0d6efd; font-weight: 700; font-size: 1.5rem;">TCHE</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item" style="--animation-order: 1">
                    <a class="nav-link active" href="#">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/documents.php">
                        <i class="fas fa-file-alt me-1"></i>Documents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/services.php">
                        <i class="fas fa-cogs me-1"></i>Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/redaction.php">
                        <i class="fas fa-pen-fancy me-1"></i>Rédaction
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pages/apropos.php">
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
                                <a class="dropdown-item" href="pages/profile.php">
                                    <i class="fas fa-user me-2 text-primary"></i>Mon Profil
                                </a>
                            </li>
                            
                            <li>
                                <a class="dropdown-item text-danger" href="pages/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="pages/login.php" class="btn btn-outline-primary rounded-pill me-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                    <a href="pages/register.php" class="btn btn-primary rounded-pill">
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
            <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
            <li class="nav-item">
                <!-- Lien direct simplifié -->
                <a class="nav-link" href="pages/documents.php">Documents</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="pages/services.php">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="pages/redaction.php">Rédaction</a></li>
            <li class="nav-item"><a class="nav-link" href="pages/apropos.php">À propos</a></li>
        </ul>
        
        <?php if ($isLoggedIn): ?>
            <div class="offcanvas-account mt-3 p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-user-circle text-primary me-2 fs-4"></i>
                    <h6 class="mb-0">Mon Compte</h6>
                </div>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="pages/profile.php" class="nav-link">
                            <i class="fas fa-user me-2 text-primary"></i>Mon Profil
                        </a>
                    </li>
                   
                    <li class="mb-2">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                        </a>
                    </li>
                </ul>
            </div>
        <?php else: ?>
            <div class="offcanvas-account mt-3 p-3">
                <div class="d-grid gap-2">
                    <a href="pages/login.php" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                    <a href="pages/register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Inscription
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hero Section -->
<section id="home" class="hero" style="background-color: rgba(255, 255, 255, 0.7); border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="container">
        <div class="row align-items-center">
            <!-- Texte Hero -->
            <div class="col-lg-6 mb-4">
                <div class="hero-text">
                    <h1 class="text-dark">
                        Trouvez vos documents académiques et professionnels
                    </h1>
                    <p class="lead text-dark mb-4">
                        Une plateforme complète pour tous vos besoins en documents de qualité.
                    </p>
                </div>
                <div class="hero-stats">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3>1000+</h3>
                            <p>Documents</p>
                        </div>
                        <div class="col-4">
                            <h3>500+</h3>
                            <p>Clients</p>
                        </div>
                        <div class="col-4">
                            <h3>100%</h3>
                            <p>Satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slider Hero -->
            <div class="col-lg-6">
                <div class="hero-slider">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <!-- Slide 1 -->
                            <div class="swiper-slide">
                                <div class="hero-image">
                                    <img src="assets/images/image-accueil-2.jpg" alt="Professional 1" class="img-fluid rounded">
                                </div>
                            </div>
                            <!-- Slide 2 -->
                            <div class="swiper-slide">
                                <div class="hero-image">
                                    <img src="assets/images/image-accueil-3.jpg" alt="Professional 2" class="img-fluid rounded">
                                </div>
                            </div>
                            <!-- Slide 3 -->
                            <div class="swiper-slide">
                                <div class="hero-image">
                                    <img src="assets/images/image-accueil-4.jpg" alt="Professional 3" class="img-fluid rounded">
                                </div>
                            </div>
                            <!-- Slide 4 -->
                            <div class="swiper-slide">
                                <div class="hero-image">
                                    <img src="assets/images/image-accueil.jpg" alt="Professional 4" class="img-fluid rounded">
                                </div>
                            </div>
                            <!-- Slide 5 -->
                            <div class="swiper-slide">
                                <div class="hero-image">
                                    <img src="assets/images/image-accueil-4.jpg" alt="Professional 5" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                        <!-- Pagination moderne -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="services">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Nos Services</h2>
            <p class="text-muted">Découvrez notre gamme complète de services</p>
        </div>
        <div class="row g-4">
            <!-- Service Card 1 -->
            <div class="col-md-4">
                <div class="service-card">
                    <div class="icon-box">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Documents Académiques</h3>
                    <p>Exposés, mémoires et documents de recherche de qualité.</p>
                    <a href="pages/documents.php" class="btn btn-outline-primary">En savoir plus</a>
                </div>
            </div>
            <!-- Service Card 2 -->
            <div class="col-md-4">
                <div class="service-card">
                    <div class="icon-box">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3>CV & Lettres</h3>
                    <p>Templates professionnels pour booster votre carrière.</p>
                    <a href="pages/documents.php" class="btn btn-outline-primary">En savoir plus</a>
                </div>
            </div>
            <!-- Service Card 3 -->
            <div class="col-md-4">
                <div class="service-card">
                    <div class="icon-box">
                        <i class="fas fa-pen-fancy"></i>
                    </div>
                    <h3>Rédaction Sur Mesure</h3>
                    <p>Service personnalisé de rédaction professionnelle.</p>
                    <a href="pages/redaction.php" class="btn btn-outline-primary">En savoir plus</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact py-5 bg-light" style="background-color: rgba(255, 255, 255, 0.7); border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Contactez-nous</h2>
            <p class="text-muted">Notre équipe est à votre disposition</p>
        </div>
        <div class="row">
            <!-- Colonne pour le formulaire de contact -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="contactForm" class="contact-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="nom" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" name="prenom" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sujet</label>
                                <select name="sujet" class="form-select" required>
                                    <option value="">Choisir un sujet</option>
                                    <option value="question">Question générale</option>
                                    <option value="support">Support technique</option>
                                    <option value="partenariat">Partenariat</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                Envoyer le message
                            </button>
                            <div id="messageResult" class="mt-3" style="display: none;"></div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Colonne pour la carte -->
            <div class="col-lg-6">
                <div class="map-container border-0 shadow-sm rounded">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126931.66373132648!2d2.5835367!3d6.4968574!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1024aa00e20a448d%3A0x103a67a10707b1b5!2sCAEB%20Porto-Novo!5e0!3m2!1sfr!2s!4v1616581234567!5m2!1sfr!2s" 
                        width="100%" 
                        height="400" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
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

<style>
.footer {
    background-color: #1a1a1a !important;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.footer a:hover {
    color: #0d6efd !important;
    transition: all 0.3s ease;
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
</style>

<script>
class ContactManager {
    constructor() {
        this.form = document.getElementById('contactForm');
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.form?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit(e);
        });
    }

    async handleSubmit(event) {
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        try {
            // Désactiver le bouton et montrer le chargement
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi...';

            // Montrer une alerte de chargement
            Swal.fire({
                title: 'Envoi en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch('process_contact.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Fermer l'alerte de chargement
                Swal.close();
                
                // Montrer l'alerte de succès
                await Swal.fire({
                    icon: 'success',
                    title: 'Message envoyé !',
                    text: 'Nous vous répondrons dans les plus brefs délais.',
                    confirmButtonText: 'Parfait !',
                    confirmButtonColor: '#28a745',
                    timer: 3000,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
                
                form.reset();
            } else {
                throw new Error(data.error || 'Erreur lors de l\'envoi');
            }

        } catch (error) {
            console.error('Erreur:', error);
            await Swal.fire({
                icon: 'error',
                title: 'Oups !',
                text: error.message || 'Une erreur est survenue lors de l\'envoi du message.',
                confirmButtonText: 'Réessayer',
                confirmButtonColor: '#dc3545'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Envoyer le message';
        }
    }

    showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        const container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.appendChild(toast);
        document.body.appendChild(container);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            container.remove();
        });
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    window.contactManager = new ContactManager();
    
    // Animation des icônes de contact au survol
    const iconBoxes = document.querySelectorAll('.icon-box-sm');
    iconBoxes.forEach(box => {
        box.addEventListener('mouseenter', function() {
            this.querySelector('i').classList.add('fa-bounce');
        });
        
        box.addEventListener('mouseleave', function() {
            this.querySelector('i').classList.remove('fa-bounce');
        });
    });
});
</script>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<style>
    .offcanvas {
        background-color: #fff;
        width: 280px;
    }
    
    .offcanvas .nav-link {
        padding: 0.5rem 1rem;
        color: #333;
    }
    
    .offcanvas .nav-link:hover {
        background-color: #f8f9fa;
    }
</style>

<?php if ($isAdmin): ?>
<a href="admin/dashboard.php" class="admin-floating" title="Administration">
    <i class="fas fa-user-shield fa-lg"></i>
</a>
<?php endif; ?>

<?php if (!$isLoggedIn): ?>
    <a href="pages/login.php" class="auth-floating login-floating" title="Connexion">
        <i class="fas fa-sign-in-alt fa-lg"></i>
    </a>
    <a href="pages/register.php" class="auth-floating register-floating" title="Inscription">
        <i class="fas fa-user-plus fa-lg"></i>
    </a>
<?php endif; ?>

<!-- Ajoutez ce script juste avant la fermeture de </body> -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.mySwiper', {
        // Paramètres essentiels
        loop: true, // Boucle infinie
        effect: 'fade', // Effet de transition
        autoplay: {
            delay: 3000, // Délai entre les slides (3 secondes)
            disableOnInteraction: false, // Continue l'autoplay après interaction
        },
        
        // Pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true, // Permet de cliquer sur les bullets
        },
        
        // Effet de transition
        fadeEffect: {
            crossFade: true
        },
        
        // Responsive breakpoints
        breakpoints: {
            320: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 1,
            },
            1024: {
                slidesPerView: 1,
            },
        },
        
        // Animation de vitesse
        speed: 1000,
        
        // Navigation tactile
        touchRatio: 1,
        touchAngle: 45,
        grabCursor: true,
    });
});
</script>
</body>
</html>