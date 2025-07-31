<?php
session_start();
require_once '../config/connexion.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Utilisateur non connecté');
    }

    // Récupérer uniquement les demandes non notifiées avec plus d'informations
    $stmt = $pdo->prepare("
        SELECT id, sujet_theme, statut, date_modification 
        FROM demandes_redaction 
        WHERE utilisateur_id = ? 
        AND notified = 0 
        AND statut != 'en_attente'
        ORDER BY date_modification DESC
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($notifications)) {
        // Marquer les notifications comme lues
        $ids = array_column($notifications, 'id');
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        
        $updateStmt = $pdo->prepare("
            UPDATE demandes_redaction 
            SET notified = 1 
            WHERE id IN ($placeholders)
        ");
        $updateStmt->execute($ids);

        // Formater les dates
        foreach ($notifications as &$notif) {
            $date = new DateTime($notif['date_modification']);
            $notif['date_modification'] = $date->format('d/m/Y H:i');
        }
    }

    echo json_encode([
        'success' => true,
        'notifications' => $notifications ?? []
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}