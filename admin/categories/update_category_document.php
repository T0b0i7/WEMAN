<?php
header('Content-Type: application/json');

try {
    // Récupération des données JSON envoyées
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception("Données JSON invalides.");
    }
    
    // Vérification des champs obligatoires
    if (empty($data['id']) || empty($data['nom']) || empty($data['statut'])) {
        throw new Exception("Les champs 'id', 'nom' et 'statut' sont obligatoires.");
    }
    
    // Inclusion de la connexion PDO
    require_once '../../config/connection.php'; // Lien de connexion mis à jour

    // Préparation de la requête de mise à jour
    $query = "UPDATE categories_documents 
              SET nom = :nom, 
                  description = :description, 
                  statut = :statut, 
                  active = :active 
              WHERE id = :id";
    $stmt = $pdo->prepare($query);
    
    $desc = isset($data['description']) ? $data['description'] : null;
    $active = isset($data['active']) && $data['active'] ? 1 : 0;
    
    $stmt->bindParam(':nom', $data['nom'], PDO::PARAM_STR);
    $stmt->bindParam(':description', $desc, PDO::PARAM_STR);
    $stmt->bindParam(':statut', $data['statut'], PDO::PARAM_STR);
    $stmt->bindParam(':active', $active, PDO::PARAM_INT);
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de la mise à jour de la catégorie.");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Catégorie modifiée avec succès.'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}