<?php
require_once '../../config/connexion.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $query = "SELECT cp.*, cd.nom as nom_categorie 
                  FROM categories_prix cp 
                  LEFT JOIN categories_documents cd ON cp.categorie_id = cd.id 
                  WHERE cp.id = ?";
                  
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $pricing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pricing) {
            // Convertir les valeurs numériques
            $pricing['prix_standard'] = (int)$pricing['prix_standard'];
            $pricing['prix_urgent'] = $pricing['prix_urgent'] ? (int)$pricing['prix_urgent'] : null;
            $pricing['seuil_mots'] = (int)$pricing['seuil_mots'];
            $pricing['prix_par_mot_supp'] = (int)$pricing['prix_par_mot_supp'];
            $pricing['delai_standard_jours'] = (int)$pricing['delai_standard_jours'];
            $pricing['delai_urgent_jours'] = $pricing['delai_urgent_jours'] ? (int)$pricing['delai_urgent_jours'] : null;
            
            echo json_encode($pricing);
            exit;
        } else {
            http_response_code(404);
            echo json_encode([
                'error' => true,
                'message' => 'Tarif non trouvé'
            ]);
            exit;
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'Erreur lors de la récupération : ' . $e->getMessage()
        ]);
        exit;
    }
} else {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => 'ID non spécifié'
    ]);
    exit;
}