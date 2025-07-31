<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'ID de catégorie non fourni'
        ]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM categories_documents WHERE id = :id");
        $stmt->execute([':id' => $data['id']]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Catégorie supprimée avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Catégorie non trouvée'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur base de données: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode de requête non valide'
    ]);
}
?>