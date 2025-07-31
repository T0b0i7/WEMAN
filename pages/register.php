<?php
// filepath: c:\xampp\htdocs\WEMAN\pages\register.php
session_start();
require_once '../config/connexion.php';

$response = array('success' => false, 'message' => '', 'utilisateur_id' => null);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = preg_replace('/[^0-9+]/', '', $_POST['telephone']); // Garde uniquement les chiffres et le +
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérification minimale du téléphone (au moins 8 chiffres)
    if (strlen($telephone) < 8) {
        $response['message'] = "Le numéro de téléphone doit contenir au moins 8 chiffres.";
        echo json_encode($response);
        exit;
    }

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $response['message'] = "Cette adresse email est déjà utilisée par un autre compte.";
        echo json_encode($response);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9]{8,}$/', $mot_de_passe)) {
        $response['message'] = "Le mot de passe doit contenir au moins 8 caractères alphanumériques.";
        echo json_encode($response);
        exit;
    }

    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, email, telephone, mot_de_passe_hash) VALUES (:prenom, :nom, :email, :telephone, :mot_de_passe_hash)");
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':mot_de_passe_hash', $mot_de_passe_hash);
        $stmt->execute();

        $utilisateur_id = $pdo->lastInsertId();
        $_SESSION['utilisateur_id'] = $utilisateur_id;

        $response['success'] = true;
        $response['message'] = "Compte créé avec succès";
        $response['utilisateur_id'] = $utilisateur_id;
    } catch (PDOException $e) {
        $response['message'] = "Erreur : " . $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <title>Inscription - WEMANTCHE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('../assets/images/image-dejected-black-man-looks-with-miserable-expression-aside-holds-pile-books-dressed-formal-clothes.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .register-card {
            border-radius: 15px;
            overflow: visible;
            position: relative;
            z-index: 2;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 75px;
        }

        .card-body {
            padding: 2rem;
            padding-top: 4rem;
        }

        .profile-container {
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
        }

        .profile-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: white;
            transition: all 0.3s ease;
            position: absolute;
            top: -50px;
        }

        .profile-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-primary {
            background-color: #0066cc;
            border-color: #0066cc;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .text-blue {
            color: #0066cc;
        }

        #emojiRain {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .emoji {
            position: absolute;
            font-size: 2rem;
            opacity: 0;
            animation: fall 8s linear infinite;
            z-index: 1;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.8);
        }

        @keyframes fall {
            0% {
                transform: translateY(-100px) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(calc(100vh + 100px)) rotate(360deg);
                opacity: 0;
            }
        }

        /* Styles spécifiques pour le champ téléphone */
        .phone-input-container {
            position: relative;
            width: 100%;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        #phone {
            border-left: none;
            padding-left: 10px;
        }

        .phone-error {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
            display: none;
        }

        @media (max-width: 768px) {
            body, html {
                background-position: center;
                background-size: cover;
            }
            
            .register-card {
                margin-top: 60px;
            }
            
            .profile-circle {
                width: 80px;
                height: 80px;
                top: -40px;
            }
        }
    </style>
</head>
<body>
<div id="emojiRain"></div>

<div class="container my-auto">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="register-card card shadow-lg border-0">
                <div class="profile-container">
                    <div class="profile-circle">
                        <img src="../assets/images/Wemantche logo  1-8.png" alt="Éducation">
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Créer un compte</h2>
                        <p class="text-muted">Rejoignez WEMANTCHE dès aujourd'hui</p>
                    </div>
                    
                    <form id="registerForm" action="register.php" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">Prénom</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="firstName" name="prenom" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Nom</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="lastName" name="nom" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="telephone" placeholder="229XXXXXXXX" required>
                            </div>
                            <div id="phone-error" class="phone-error">Veuillez entrer un numéro de téléphone valide</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="mot_de_passe" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small id="passwordFeedback" class="form-text"></small>
                        </div>

                        <div class="mb-4">
                            <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmMotdepasse" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">S'inscrire</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Déjà inscrit ? 
                            <a href="login.php" class="text-primary">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Animation des emojis qui tombent
    const emojis = ['📚', '🎓', '✏️', '🏫', '🧑‍🎓', '📝', '👨‍🏫', '⚛️', '🧪', '🧮', '🌍', '🔬', '💡', '🏆', '📖'];
    
    function createFallingEmoji() {
        const emoji = document.createElement('div');
        emoji.className = 'emoji';
        emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        emoji.style.left = `${5 + Math.random() * 90}vw`;
        emoji.style.animationDuration = `${5 + Math.random() * 5}s`;
        emoji.style.animationDelay = `${Math.random() * 2}s`;
        emoji.style.fontSize = `${1 + Math.random() * 2}rem`;
        document.getElementById('emojiRain').appendChild(emoji);
        setTimeout(() => emoji.remove(), 10000);
    }
    
    let emojiInterval;
    function startEmojiAnimation() {
        for (let i = 0; i < 20; i++) {
            setTimeout(createFallingEmoji, i * 200);
        }
        return setInterval(createFallingEmoji, 400);
    }
    
    // Validation du téléphone
    function validatePhone() {
        const phoneInput = document.querySelector("#phone");
        const phoneError = document.querySelector("#phone-error");
        const phoneNumber = phoneInput.value.trim();
        
        if (phoneNumber.length === 0) {
            phoneInput.classList.add('is-invalid');
            phoneError.style.display = 'block';
            phoneError.textContent = "Le numéro de téléphone est requis";
            return false;
        }
        
        // Accepte tout format de numéro avec au moins 8 chiffres
        const phoneRegex = /^[0-9+\s-]{8,}$/;
        if (!phoneRegex.test(phoneNumber.replace(/\s/g, ''))) {
            phoneInput.classList.add('is-invalid');
            phoneError.style.display = 'block';
            phoneError.textContent = "Veuillez entrer un numéro de téléphone valide";
            return false;
        }
        
        phoneInput.classList.remove('is-invalid');
        phoneError.style.display = 'none';
        return true;
    }

    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        password.type = password.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye-slash');
        icon.classList.toggle('fa-eye');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPassword = document.getElementById('confirmPassword');
        const icon = this.querySelector('i');
        confirmPassword.type = confirmPassword.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye-slash');
        icon.classList.toggle('fa-eye');
    });

    // Validation du mot de passe
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const feedback = document.getElementById('passwordFeedback');
        const regex = /^[a-zA-Z0-9]{8,}$/;

        if (!regex.test(password)) {
            feedback.textContent = "Le mot de passe doit contenir au moins 8 caractères alphanumériques.";
            feedback.style.color = "red";
        } else {
            feedback.textContent = "Mot de passe valide.";
            feedback.style.color = "green";
        }
    });

    // Gestion du formulaire
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const phoneInput = document.querySelector("#phone"); // Définir phoneInput ici
        const isPhoneValid = validatePhone();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const passwordRegex = /^[a-zA-Z0-9]{8,}$/;

        if (!passwordRegex.test(password)) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Le mot de passe doit contenir au moins 8 caractères alphanumériques.'
            });
            return;
        }

        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Les mots de passe ne correspondent pas.'
            });
            return;
        }

        if (!isPhoneValid) {
            phoneInput.focus();
            Swal.fire({
                icon: 'error',
                title: 'Numéro invalide',
                text: 'Veuillez entrer un numéro de téléphone valide'
            });
            return;
        }

        const formData = new FormData(this);

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Compte créé avec succès',
                    html: `Bienvenue ${document.getElementById('firstName').value} !<br><br>Vous allez être redirigé vers le questionnaire.`,
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = `questionnaire.html?utilisateur_id=${data.utilisateur_id}`;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur est survenue. Veuillez réessayer.'
            });
            console.error('Erreur:', error);
        });
    });

    // Initialisation après le chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        startEmojiAnimation();
        
        const phoneInput = document.querySelector("#phone");
        phoneInput.addEventListener('input', validatePhone);
        phoneInput.addEventListener('blur', validatePhone);
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(emojiInterval);
        } else {
            emojiInterval = startEmojiAnimation();
        }
    });
</script>
</body>
</html>