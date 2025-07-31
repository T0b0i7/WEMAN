<?php
session_start();
require_once '../config/connexion.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Utilisateur non connecté');
    }

    // Récupérer uniquement les demandes non notifiées
    $stmt = $pdo->prepare("
        SELECT id, sujet, statut 
        FROM demandes_redaction 
        WHERE utilisateur_id = ? 
        AND notified = 0 
        AND statut != 'en_attente'
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($demandes)) {
        // Marquer les demandes comme notifiées
        $ids = array_column($demandes, 'id');
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        $updateStmt = $pdo->prepare("
            UPDATE demandes_redaction 
            SET notified = 1 
            WHERE id IN ($placeholders)
        ");
        $updateStmt->execute($ids);
    }

    echo json_encode([
        'success' => true,
        'demandes' => $demandes
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}