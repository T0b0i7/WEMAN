<?php
require_once '../../config/connexion.php';
header('Content-Type: application/json');

try {
    // Récupération des classes actives
    $classesQuery = $pdo->query("SELECT id, nom FROM classes WHERE actif = 1 ORDER BY ordre");
    $classes = $classesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Récupération des filières actives
    $filieresQuery = $pdo->query("SELECT id, nom FROM filieres WHERE actif = 1 ORDER BY nom");
    $filieres = $filieresQuery->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'classes' => $classes,
        'filieres' => $filieres
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}