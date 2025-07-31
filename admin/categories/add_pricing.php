<?php
header('Content-Type: application/json');
require_once '../../config/connexion.php';

try {
    // Récupérer les données JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Données JSON invalides');
    }

    // Validation des données requises
    if (!isset($data['categorie_id']) || !isset($data['prix_standard'])) {
        throw new Exception('Données manquantes');
    }

    // Préparation de la requête SQL
    $sql = "INSERT INTO categories_prix (
        categorie_id, 
        prix_standard, 
        prix_urgent, 
        seuil_mots, 
        prix_par_mot_supp, 
        delai_standard_jours, 
        delai_urgent_jours, 
        niveau_etude
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    // Exécution de la requête
    $result = $stmt->execute([
        $data['categorie_id'],
        $data['prix_standard'],
        $data['prix_urgent'] ?? null,
        $data['seuil_mots'] ?? 1000,
        $data['prix_par_mot_supp'] ?? 500,
        $data['delai_standard_jours'] ?? 7,
        $data['delai_urgent_jours'] ?? null,
        $data['niveau_etude'] ?? 'RAS'
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Tarif ajouté avec succès',
            'id' => $pdo->lastInsertId()
        ]);
    } else {
        throw new Exception('Erreur lors de l\'ajout du tarif');
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur base de données: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}