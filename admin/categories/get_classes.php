<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, nom FROM classes WHERE actif = 1 ORDER BY ordre ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($classes);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}