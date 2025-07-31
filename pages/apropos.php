<?php
session_start();
require_once 'protect-source.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <title>À propos de WEMANTCHE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;600&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;600&family=Open+Sans:wght@400&display=swap" rel="stylesheet">
    <style>
        .fade-in {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .fade-in.visible {
            opacity: 1;
        }
        .snowflake {
            position: absolute;
            top: -10px;
            background: white;
            border-radius: 50%;
            opacity: 0.8;
            pointer-events: none;
            animation: fall linear infinite;
        }
        @keyframes fall {
            to {
                transform: translateY(100vh);
            }
        }
        .about-section {
            position: relative;
            min-height: 100vh;
            width: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                        url('../assets/images/study-group-african-people.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: #fff;
            padding: 80px 0;
            margin: 0;
        }

        /* Adaptation pour les écrans mobiles */
        @media (max-width: 768px) {
            .about-section {
                padding: 30px 15px; /* Réduire les marges pour les petits écrans */
                background-size: cover; /* Assure que l'image reste en mode "cover" */
            }
        }

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

        /* Style du glossaire */
        .glossary-container {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .carousel-glossary {
            position: relative;
            overflow: hidden;
        }

        .carousel-inner {
            transition: transform 0.5s ease;
        }

        .carousel-item {
            padding: 2rem;
        }

        .glossary-title {
            color: #ffffff;
            font-size: 2.8rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #0d6efd;
        }

        .glossary-section {
            margin-bottom: 2rem;
        }

        .glossary-section h2 {
            color: #0d6efd;
            font-size: 2rem;
            font-weight: 600;
            margin: 2rem 0 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #0d6efd;
        }

        .glossary-section h3 {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
        }

        .glossary-text {
            color: #ffffff;
            line-height: 1.8;
            font-size: 1.1rem;
            text-align: justify;
        }

        .glossary-list {
            list-style: none;
            padding-left: 0;
            margin: 1.5rem 0;
        }

        .glossary-list li {
            margin-bottom: 1rem;
            padding-left: 2rem;
            position: relative;
            line-height: 1.6;
        }

        .glossary-list li::before {
            content: '>';
            color: #0d6efd;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        /* Controls du carrousel */
        .carousel-control-prev,
        .carousel-control-next {
            width: 50px;
            height: 50px;
            background: rgba(13, 110, 253, 0.3);
            border-radius: 50%;
            margin: 0 2rem;
            opacity: 1;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background: rgba(13, 110, 253, 0.5);
        }

        .carousel-indicators {
            margin-bottom: -3rem;
        }

        .carousel-indicators button {
            width: 12px !important;
            height: 12px !important;
            border-radius: 50%;
            background-color: #0d6efd !important;
            margin: 0 8px;
            opacity: 0.7;
        }

        .carousel-indicators button.active {
            opacity: 1;
            transform: scale(1.2);
        }
    </style>
</head>
<body class="bg-light">

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
                    <a class="nav-link active" href="apropos.php">
                        <i class="fas fa-info-circle me-1"></i>À propos
                    </a>
                </li>
            </ul>
            <div class="d-none d-lg-flex align-items-center">
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
            <li class="nav-item"><a class="nav-link" href="redaction.php">Rédaction sur mesure</a></li>
           
            <li class="nav-item"><a class="nav-link" href="apropos.php">À propos</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Mon Compte</a></li>
        </ul>
    </div>
</div>
    <section class="about-section">
        <div class="container mt-5 pt-5">
            <div class="glossary-container">
                <div id="glossaryCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#glossaryCarousel" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#glossaryCarousel" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#glossaryCarousel" data-bs-slide-to="2"></button>
                        <button type="button" data-bs-target="#glossaryCarousel" data-bs-slide-to="3"></button>
                    </div>

                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="glossary-section">
                                <h1 class="glossary-title fade-in">À PROPOS DE WEMANTCHE</h1>
                                <div class="glossary-text fade-in">
                                    <p>WEMANTCHE est une plateforme e-commerce innovante spécialisée dans la vente et la personnalisation de documents académiques et professionnels. Elle offre une solution rapide, simple et efficace pour accéder à des documents de qualité, adaptés aux besoins des étudiants, chercheurs d'emploi et professionnels.</p>
                                    
                                    <h2>Contexte et Genèse du Projet</h2>
                                    <p>Dans un monde où la digitalisation des services devient une nécessité, l'accès à des documents professionnels et académiques bien structurés reste un défi pour de nombreux étudiants et travailleurs.</p>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="glossary-section">
                                <h2>Mission et Vision</h2>
                                <div class="glossary-text">
                                    <h3>Mission</h3>
                                    <p>WEMANTCHE s'engage à offrir des documents académiques et professionnels de haute qualité pour aider ses utilisateurs à atteindre leurs objectifs.</p>
                                    <h3>Vision</h3>
                                    <p>WEMANTCHE ambitionne de devenir la référence en matière de vente et de personnalisation de documents numériques en Afrique de l'Ouest.</p>
                                </div>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="glossary-section">
                                <h2>Valeurs Fondamentales & Services</h2>
                                <ul class="glossary-list">
                                    <li>Accessibilité : Permettre à tous d'accéder à des documents de qualité</li>
                                    <li>Qualité et professionnalisme : Garantir des contenus bien structurés</li>
                                    <li>Innovation et digitalisation : Exploiter la technologie pour simplifier l'accès</li>
                                    <li>Satisfaction client : Assurer un service réactif et efficace</li>
                                </ul>
                                <h3>Nos Services</h3>
                                <ul class="glossary-list">
                                    <li>Documents académiques et professionnels</li>
                                    <li>CV et lettres de motivation personnalisés</li>
                                    <li>Mémoires et rapports de stage</li>
                                </ul>
                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="glossary-section">
                                <h2>Notre Impact</h2>
                                <div class="glossary-text">
                                    <p>Basé à Porto-Novo, Bénin, WEMANTCHE s'impose comme un acteur clé de la digitalisation des documents académiques et professionnels en Afrique de l'Ouest. Notre plateforme accessible 24/7 permet aux utilisateurs d'obtenir leurs documents en quelques clics.</p>
                                    <p class="mt-4"><em>Avec WEMANTCHE, gagnez du temps et accédez aux meilleurs documents !</em></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#glossaryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#glossaryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            let delay = 0;
            elements.forEach(element => {
                setTimeout(() => {
                    element.classList.add('visible');
                }, delay);
                delay += 200; // Delay between each element's fade-in
            });


            // Effet de neige (inchangé)
            const snowContainer = document.createElement('div');
            snowContainer.style.position = 'fixed';
            snowContainer.style.top = '0';
            snowContainer.style.left = '0';
            snowContainer.style.width = '100%';
            snowContainer.style.height = '100%';
            snowContainer.style.pointerEvents = 'none';
            snowContainer.style.zIndex = '9999';
            document.body.appendChild(snowContainer);

            function createSnowflake() {
                const snowflake = document.createElement('div');
                snowflake.classList.add('snowflake');
                snowflake.style.left = Math.random() * 100 + 'vw';
                snowflake.style.width = Math.random() * 10 + 5 + 'px';
                snowflake.style.height = snowflake.style.width;
                snowflake.style.animationDuration = Math.random() * 3 + 2 + 's';
                snowContainer.appendChild(snowflake);

                setTimeout(() => {
                    snowflake.remove();
                }, parseFloat(snowflake.style.animationDuration) * 1000);
            }

            setInterval(createSnowflake, 200);
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Création des boutons dans un container
    const buttonContainer = document.createElement('div');
    buttonContainer.style.position = 'fixed';
    buttonContainer.style.bottom = '20px';
    buttonContainer.style.right = '20px';
    buttonContainer.style.zIndex = '1000';
    buttonContainer.style.display = 'flex';
    buttonContainer.style.gap = '10px';

    // Bouton de lecture avec nouveau design
    const readButton = document.createElement('button');
    readButton.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-headphones"></i>
            <span style="font-size: 10px; margin-top: 4px;">Écouter</span>
        </div>
    `;
    readButton.title = "Lecture vocale";
    Object.assign(readButton.style, {
        backgroundColor: '#5C37B7',
        color: '#fff',
        border: 'none',
        borderRadius: '15px',
        padding: '12px 20px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        boxShadow: '0 4px 15px rgba(92, 55, 183, 0.4)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        minWidth: '100px'
    });

    // Bouton d'arrêt avec nouveau design
    const stopButton = document.createElement('button');
    stopButton.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center;">
            <i class="fas fa-stop"></i>
            <span style="font-size: 10px; margin-top: 4px;">Arrêter</span>
        </div>
    `;
    stopButton.title = "Arrêter la lecture";
    Object.assign(stopButton.style, {
        backgroundColor: '#FF4B6E',
        color: '#fff',
        border: 'none',
        borderRadius: '15px',
        padding: '12px 20px',
        cursor: 'pointer',
        transition: 'all 0.3s ease',
        boxShadow: '0 4px 15px rgba(255, 75, 110, 0.4)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        minWidth: '100px'
    });

    // Effets de survol
    readButton.onmouseover = () => {
        readButton.style.transform = 'translateY(-2px)';
        readButton.style.boxShadow = '0 6px 20px rgba(92, 55, 183, 0.6)';
    };
    readButton.onmouseout = () => {
        readButton.style.transform = 'translateY(0)';
        readButton.style.boxShadow = '0 4px 15px rgba(92, 55, 183, 0.4)';
    };

    stopButton.onmouseover = () => {
        stopButton.style.transform = 'translateY(-2px)';
        stopButton.style.boxShadow = '0 6px 20px rgba(255, 75, 110, 0.6)';
    };
    stopButton.onmouseout = () => {
        stopButton.style.transform = 'translateY(0)';
        stopButton.style.boxShadow = '0 4px 15px rgba(255, 75, 110, 0.4)';
    };

    // Ajout des boutons au container
    buttonContainer.appendChild(readButton);
    buttonContainer.appendChild(stopButton);
    document.body.appendChild(buttonContainer);

    // Gestion de la lecture
    let speech = null;
    let isReading = false;

    readButton.addEventListener('click', function() {
        if (isReading) {
            window.speechSynthesis.pause();
            readButton.querySelector('i').className = 'fas fa-play';
            readButton.querySelector('span').textContent = 'Reprendre';
            isReading = false;
        } else {
            if (speech === null) {
                // Récupérer le texte de tous les éléments avec la classe glossary-text
                const textElements = document.querySelectorAll('.glossary-text');
                let textToRead = '';
                textElements.forEach(element => {
                    textToRead += element.innerText + ' ';
                });

                speech = new SpeechSynthesisUtterance();
                speech.text = textToRead;
                speech.lang = 'fr-FR';
                speech.rate = 0.9; // Vitesse légèrement plus lente
                speech.pitch = 0.9; // Ton plus grave pour une voix masculine

                // Sélection d'une voix masculine en français
                window.speechSynthesis.onvoiceschanged = function() {
                    const voices = window.speechSynthesis.getVoices();
                    const frenchVoices = voices.filter(voice => voice.lang.includes('fr'));
                    // Chercher une voix masculine en français
                    const maleVoice = frenchVoices.find(voice => 
                        voice.name.toLowerCase().includes('thomas') || 
                        voice.name.toLowerCase().includes('male') ||
                        voice.name.toLowerCase().includes('homme'));
                    
                    if (maleVoice) {
                        speech.voice = maleVoice;
                    } else if (frenchVoices.length > 0) {
                        speech.voice = frenchVoices[0];
                    }
                };

                speech.onend = function() {
                    isReading = false;
                    readButton.querySelector('i').className = 'fas fa-headphones';
                    readButton.querySelector('span').textContent = 'Écouter';
                    readButton.style.backgroundColor = '#5C37B7';
                    speech = null;
                };

                speech.onpause = function() {
                    isReading = false;
                    readButton.querySelector('i').className = 'fas fa-play';
                    readButton.querySelector('span').textContent = 'Reprendre';
                };

                speech.onresume = function() {
                    isReading = true;
                    readButton.querySelector('i').className = 'fas fa-pause';
                    readButton.querySelector('span').textContent = 'Pause';
                };

                window.speechSynthesis.speak(speech);
            } else {
                window.speechSynthesis.resume();
            }
            
            readButton.querySelector('i').className = 'fas fa-pause';
            readButton.querySelector('span').textContent = 'Pause';
            isReading = true;
        }
    });

    stopButton.addEventListener('click', function() {
        if (speech) {
            window.speechSynthesis.cancel();
            speech = null;
            isReading = false;
            readButton.querySelector('i').className = 'fas fa-headphones';
            readButton.querySelector('span').textContent = 'Écouter';
            readButton.style.backgroundColor = '#5C37B7';
        }
    });

    // Nettoyage lors du déchargement de la page
    window.addEventListener('beforeunload', function() {
        if (speech) {
            window.speechSynthesis.cancel();
        }
    });
});
</script>
</body>
</html>