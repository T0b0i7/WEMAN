<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT cp.*, c.nom as nom_categorie 
            FROM categories_prix cp 
            LEFT JOIN categories_documents c ON cp.categorie_id = c.id 
            ORDER BY c.nom ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($prices);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}