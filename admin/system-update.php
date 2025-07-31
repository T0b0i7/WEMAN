<?php
require_once('../config/connexion.php');
require_once('../pages/protect-source.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour système - Support technique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .support-card {
            max-width: 600px;
            margin: 100px auto;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .support-icon {
            font-size: 4rem;
            color: #4361ee;
            margin-bottom: 1.5rem;
        }
        .support-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4361ee;
        }
        .support-text {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="support-card bg-white">
            <div class="text-center">
                <i class="fas fa-headset support-icon"></i>
                <h2 class="mb-4">Contactez notre support technique</h2>
                <div class="alert alert-info mb-4">
                    <p class="mb-0">Pour procéder à la mise à jour du système, veuillez contacter notre équipe de support technique :</p>
                </div>
                <div class="support-number mb-3">
                    <i class="fas fa-phone-alt me-2"></i>
                    01 57 00 24 27
                </div>
                <p class="support-text mb-4">
                    Notre équipe est disponible du lundi au vendredi<br>
                    de 9h00 à 18h00
                </p>
                <div class="alert alert-warning">
                    <small>
                        <i class="fas fa-info-circle me-2"></i>
                        Ayez votre identifiant système à portée de main lors de l'appel
                    </small>
                </div>
                <a href="dashboard.php" class="btn btn-primary mt-4">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>