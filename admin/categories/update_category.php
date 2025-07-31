<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    // Récupérer les données envoyées
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['statut'])) {
        throw new Exception('Données manquantes');
    }

    // Préparer la requête
    $sql = "UPDATE categories_documents 
            SET nom = :nom,
                description = :description,
                statut = :statut,
                active = :active
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    
    // Exécuter la requête
    $success = $stmt->execute([
        'id' => $data['id'],
        'nom' => $data['name'],
        'description' => $data['description'],
        'statut' => $data['statut'],
        'active' => $data['active']
    ]);

    if (!$success) {
        throw new Exception('Erreur lors de la modification');
    }

    // Réponse en JSON
    echo json_encode([
        'success' => true,
        'message' => 'Catégorie modifiée avec succès'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>