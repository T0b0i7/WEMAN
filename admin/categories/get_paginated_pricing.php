<?php

require_once '../../config/connexion.php';
header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

try {
    // Compter le total
    $total = $pdo->query("SELECT COUNT(*) FROM categories_prix")->fetchColumn();
    
    // Récupérer les données paginées
    $query = "SELECT cp.*, cd.nom as nom_categorie 
              FROM categories_prix cp 
              LEFT JOIN categories_documents cd ON cp.categorie_id = cd.id 
              ORDER BY cd.nom 
              LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$limit, $offset]);
    $pricing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'data' => $pricing,
        'total' => $total,
        'pages' => ceil($total / $limit),
        'current_page' => $page
    ]);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}