<?php
// Désactiver l'affichage des erreurs PHP dans la sortie
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurer l'en-tête pour JSON
header('Content-Type: application/json');

try {
    // Récupérer les données JSON
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Vérifier les données requises
    if (!$data || !isset($data['id'])) {
        throw new Exception('Données requises manquantes');
    }

    // Connexion à la base de données
    require_once '../../config/connexion.php';
    
    // Préparer la requête SQL
    $sql = "UPDATE categories_prix SET 
            categorie_id = :categorie_id,
            prix_standard = :prix_standard,
            prix_urgent = :prix_urgent,
            seuil_mots = :seuil_mots,
            prix_par_mot_supp = :prix_par_mot_supp,
            delai_standard_jours = :delai_standard_jours,
            delai_urgent_jours = :delai_urgent_jours,
            niveau_etude = :niveau_etude
            WHERE id = :id";

    // Préparer la requête
    $stmt = $pdo->prepare($sql);

    // Lier les paramètres
    $params = [
        ':id' => intval($data['id']),
        ':categorie_id' => intval($data['categorie_id']),
        ':prix_standard' => intval($data['prix_standard']),
        ':prix_urgent' => $data['prix_urgent'] ? intval($data['prix_urgent']) : null,
        ':seuil_mots' => isset($data['seuil_mots']) ? intval($data['seuil_mots']) : 1000,
        ':prix_par_mot_supp' => isset($data['prix_par_mot_supp']) ? intval($data['prix_par_mot_supp']) : 500,
        ':delai_standard_jours' => isset($data['delai_standard_jours']) ? intval($data['delai_standard_jours']) : 7,
        ':delai_urgent_jours' => $data['delai_urgent_jours'] ? intval($data['delai_urgent_jours']) : null,
        ':niveau_etude' => $data['niveau_etude'] ?? 'RAS'
    ];

    // Exécuter la requête
    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true,
            'message' => 'Tarif mis à jour avec succès'
        ]);
    } else {
        throw new Exception('Erreur lors de la mise à jour');
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}