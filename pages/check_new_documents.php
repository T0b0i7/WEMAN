<?php
require_once '../config/connexion.php';

header('Content-Type: application/json');

try {
    // Vérifier les documents ajoutés dans les dernières 24 heures
    $sql = "SELECT COUNT(*) as count, GROUP_CONCAT(titre SEPARATOR '||') as titles 
            FROM documents 
            WHERE cree_a >= NOW() - INTERVAL 24 HOUR";
    
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => intval($result['count']),
        'titles' => $result['titles'] ? explode('||', $result['titles']) : []
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}