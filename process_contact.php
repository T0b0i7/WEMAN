<?php
require_once 'config/connexion.php';

header('Content-Type: application/json');

try {
    // Validation des données
    $required_fields = ['nom', 'prenom', 'email', 'sujet', 'message'];
    $data = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Le champ $field est requis");
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Validation de l'email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("L'adresse email n'est pas valide");
    }

    // Insertion dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO contacts (nom, prenom, email, sujet, message, statut)
        VALUES (:nom, :prenom, :email, :sujet, :message, 'nouvelle')
    ");

    $success = $stmt->execute([
        'nom' => $data['nom'],
        'prenom' => $data['prenom'],
        'email' => $data['email'],
        'sujet' => $data['sujet'],
        'message' => $data['message']
    ]);

    if (!$success) {
        throw new Exception("Erreur lors de l'enregistrement du message");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Message envoyé avec succès'
    ]);

} catch (Exception $e) {
    error_log("Erreur contact form: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}