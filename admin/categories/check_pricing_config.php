<?php
header('Content-Type: application/json');
require_once '../../config/connexion.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "SELECT id FROM categories_prix WHERE 
            categorie_id = ? AND 
            prix_standard = ? AND 
            prix_urgent = ? AND 
            niveau_etude = ?";
    
    $params = [
        $data['categorie_id'],
        $data['prix_standard'],
        $data['prix_urgent'],
        $data['niveau_etude']
    ];

    // Si c'est une modification, exclure l'ID actuel
    if (isset($data['id'])) {
        $sql .= " AND id != ?";
        $params[] = $data['id'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'exists' => $stmt->fetch() !== false
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}