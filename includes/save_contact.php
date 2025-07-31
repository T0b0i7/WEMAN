<?php
require_once '../config/connexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération des données
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $sujet = filter_input(INPUT_POST, 'sujet', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

        // Validation
        if (!$nom || !$prenom || !$email || !$sujet || !$message) {
            throw new Exception('Tous les champs sont obligatoires');
        }

        // Préparation et exécution de la requête
        $sql = "INSERT INTO contacts (nom, prenom, email, sujet, message) VALUES (:nom, :prenom, :email, :sujet, :message)";
        $stmt = $connexion->prepare($sql);
        
        $result = $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':sujet' => $sujet,
            ':message' => $message
        ]);

        if ($result) {
            // Ajouter la notification
            $notification_sql = "INSERT INTO notifications (type, message) VALUES (:type, :message)";
            $notification_stmt = $connexion->prepare($notification_sql);
            $notification_stmt->execute([
                ':type' => 'contact',
                ':message' => "Nouveau message de {$prenom} {$nom} concernant : {$sujet}"
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Message envoyé avec succès!'
            ]);
        } else {
            throw new Exception('Erreur lors de l\'enregistrement');
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
?>
