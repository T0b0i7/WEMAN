<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $sql = "SELECT id FROM categories_prix WHERE 
            categorie_id = :categorie_id AND 
            niveau_etude = :niveau_etude AND 
            prix_standard = :prix_standard AND 
            prix_urgent = :prix_urgent AND 
            seuil_mots = :seuil_mots AND 
            delai_standard_jours = :delai_standard_jours AND 
            delai_urgent_jours = :delai_urgent_jours";
    
    if (isset($data['id'])) {
        $sql .= " AND id != :id";
    }

    $stmt = $pdo->prepare($sql);
    
    $params = [
        ':categorie_id' => $data['categorie_id'],
        ':niveau_etude' => $data['niveau_etude'],
        ':prix_standard' => $data['prix_standard'],
        ':prix_urgent' => $data['prix_urgent'],
        ':seuil_mots' => $data['seuil_mots'],
        ':delai_standard_jours' => $data['delai_standard_jours'],
        ':delai_urgent_jours' => $data['delai_urgent_jours']
    ];
    
    if (isset($data['id'])) {
        $params[':id'] = $data['id'];
    }

    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'exists' => $result !== false,
        'id' => $result ? $result['id'] : null
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}