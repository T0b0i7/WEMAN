<?php
require_once '../../config/connexion.php';
require_once 'notifications.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

// Récupérer la dernière vérification
$lastCheck = isset($_SESSION['last_check']) ? $_SESSION['last_check'] : date('Y-m-d H:i:s', strtotime('-1 day'));

try {
    // Vérifier les nouvelles demandes
    $sql = "SELECT dr.*, u.nom, u.prenom 
            FROM demandes_redaction dr 
            JOIN utilisateurs u ON dr.utilisateur_id = u.id 
            WHERE dr.date_creation > ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$lastCheck]);
    $newDemandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Créer des notifications pour les nouvelles demandes
    foreach($newDemandes as $demande) {
        $message = "Nouvelle demande de " . $demande['prenom'] . " " . $demande['nom'] . 
                  " pour un document de type " . $demande['type_document'];
        createNotification($_SESSION['user_id'], $message, 'new_request');
    }

    // Mettre à jour le timestamp de dernière vérification
    $_SESSION['last_check'] = date('Y-m-d H:i:s');

    // Récupérer toutes les notifications non lues
    $notifications = getUnreadNotifications($_SESSION['user_id']);
    
    echo json_encode([
        'notifications' => $notifications,
        'newDemandes' => count($newDemandes)
    ]);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
