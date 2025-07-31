<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
    exit;
}

try {
    $sql = "SELECT cp.*, cd.nom as nom_categorie 
            FROM categories_prix cp 
            LEFT JOIN categories_documents cd ON cp.categorie_id = cd.id 
            WHERE cp.id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $_GET['id']]);
    $price = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$price) {
        http_response_code(404);
        echo json_encode(['error' => 'Tarif non trouvé']);
        exit;
    }
    
    echo json_encode($price);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}