<?php
header('Content-Type: application/json');
require_once '../../config/connexion.php';

try {
    // Récupérer l'ID depuis l'URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        throw new Exception('ID non spécifié');
    }

    // Vérifier si le tarif existe
    $checkSql = "SELECT id FROM categories_prix WHERE id = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$id]);
    
    if (!$checkStmt->fetch()) {
        throw new Exception('Tarif non trouvé');
    }

    // Supprimer le tarif
    $sql = "DELETE FROM categories_prix WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$id]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Tarif supprimé avec succès'
        ]);
    } else {
        throw new Exception('Erreur lors de la suppression');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}