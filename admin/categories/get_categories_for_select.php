<?php
require_once '../../config/connexion.php';

try {
    $query = "SELECT id, nom FROM categories_documents WHERE active = 1 ORDER BY nom ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($categories);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Erreur de récupération des catégories: ' . $e->getMessage()]);
}