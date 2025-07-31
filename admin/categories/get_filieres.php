<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, nom FROM filieres WHERE actif = 1 ORDER BY niveau ASC, nom ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($filieres);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}