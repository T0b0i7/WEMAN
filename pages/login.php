<?php
session_start();
require_once '../config/connexion.php';

if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Déconnexion réussie",
                text: "À bientôt !",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
                background: "var(--bg-card)",
                color: "var(--text-white)"
            });
        });
    </script>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['mot_de_passe'];
    $rememberMe = isset($_POST['remember_me']);

    try {
        if (!$email) {
            throw new Exception('Email invalide');
        }

        if (!preg_match('/^[a-zA-Z0-9]{8,}$/', $password)) {
            throw new Exception('Le mot de passe doit contenir au moins 8 caractères alphanumériques.');
        }

        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email AND statut = 'actif'");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['role'] = $user['role'];

            // Mise à jour de la dernière connexion
            $updateStmt = $pdo->prepare("UPDATE utilisateurs SET derniere_action = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            // Gestion du Remember Me
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                $updateToken = $pdo->prepare("UPDATE utilisateurs SET remember_token = ? WHERE id = ?");
                $updateToken->execute([$token, $user['id']]);
                setcookie('remember_token', $token, time() + (86400 * 30), '/');
            }

            echo json_encode([
                'success' => true,
                'redirect' => $user['role'] === 'administrateur' ? '../admin/dashboard.php' : '../index.php',
                'userName' => $user['nom'],
                'role' => $user['role'],
                'lastLogin' => date('d/m/Y à H:i')
            ]);
        } else {
            throw new Exception('Identifiants incorrects');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="../assets/images/WEMANTCHE LOGO p 2.png">
    <title>Connexion - WEMANTCHE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('../assets/images/indoor-shot-pleased-dark-skinned-man-leans-pile-books-dressed-casual-sweater-wears-round-spectacles.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            overflow-x: hidden;
        }

        @media (max-width: 768px) {
            body, html {
                background-position: center;
                background-size: cover;
            }
        }

        .main-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        .profile-container {
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
        }

        .profile-circle {
            width: 100px; /* Réduit de 150px à 100px */
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white; /* Réduit de 5px à 4px */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background: white;
            transition: all 0.3s ease;
            position: absolute;
            top: -50px; /* Ajusté de -75px à -50px */
        }

        @media (max-width: 768px) {
            .profile-circle {
                width: 80px; /* Encore plus petit sur mobile */
                height: 80px;
                top: -40px;
                border: 3px solid white;
            }

            .login-card {
                margin: 50px 15px; /* Ajustement des marges sur mobile */
            }

            .card-body {
                padding: 1.5rem;
                padding-top: 3rem; /* Réduit l'espace en haut */
            }
        }

        .profile-circle:hover {
            transform: translateY(-5px) rotate(5deg);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .profile-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        .login-card {
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div id="emojiRain"></div>

    <div class="main-container">
        <div class="container my-auto">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="login-card card shadow-lg border-0">
                        <div class="profile-container">
                            <div class="profile-circle">
                                <img src="../assets/images/Wemantche logo  1-8.png" alt="Éducation">
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h2 class="fw-bold">Connexion</h2>
                                <p class="text-muted">Accédez à votre compte WEMANTCHE</p>
                            </div>
                            
                            <form id="loginForm" action="login.php" method="POST">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="mot_de_passe" required>
                                    </div>
                                    <small id="passwordFeedback" class="form-text"></small>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember_me" required>
                                        <label class="form-check-label" for="remember">
                                            Accepter les <a href="404.php" target="_blank" class="text-primary">conditions de confidentialité</a>
                                        </label>
                                    </div>
                                    <a href="questionnaire_forgot_password.html" class="text-primary text-decoration-none">Mot de passe oublié ?</a>
                                </div>

                                <div class="alert alert-warning" role="alert">
                                    Vous n'avez que 3 chances pour récupérer votre mot de passe.
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="mb-0">Pas encore de compte ? 
                                    <a href="register.php" class="text-primary">S'inscrire</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Gestion du formulaire avec animation de bienvenue
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Vérification du formulaire
            if (!this.checkValidity()) {
                e.stopPropagation();
                return;
            }

            // Afficher le chargement
            Swal.fire({
                title: 'Connexion en cours...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(this);
            
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Message de bienvenue personnalisé selon le rôle
                    const welcomeMessage = data.role === 'administrateur' 
                        ? `Bienvenue Admin ${data.userName} ! 🎉` 
                        : `Bienvenue ${data.userName} ! 🎉`;
                        
                    Swal.fire({
                        title: welcomeMessage,
                        html: `Dernière connexion: ${data.lastLogin}`,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    throw new Error(data.message || 'Échec de la connexion');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                Swal.fire({
                    title: 'Erreur',
                    text: error.message || 'Une erreur est survenue lors de la connexion',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0066cc'
                });
            });
        });

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
            for (let i = 0; i < 15; i++) {
                setTimeout(createFallingEmoji, i * 300);
            }
            return setInterval(createFallingEmoji, 500);
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            emojiInterval = startEmojiAnimation();
        });
        
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(emojiInterval);
            } else {
                emojiInterval = startEmojiAnimation();
            }
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

        // Protection contre l'inspection
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('keydown', e => {
            if ((e.ctrlKey && e.key === 'u') || 
                (e.ctrlKey && e.shiftKey && e.key === 'i') || 
                (e.ctrlKey && e.shiftKey && e.key === 'j') ||
                (e.key === 'F12') ||
                (e.ctrlKey && e.key === 's')) {
                e.preventDefault();
                Swal.fire({
                    title: 'Action non autorisée',
                    text: 'Cette fonctionnalité est désactivée pour des raisons de sécurité.',
                    icon: 'warning',
                    confirmButtonColor: '#0066cc'
                });
            }
        });
    </script>
</body>
</html>