<?php
require_once '../../config/connexion.php';
header('Content-Type: application/json');

try {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($id)) {
        throw new Exception('ID invalide');
    }

    $stmt = $pdo->prepare("DELETE FROM filieres WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Filière supprimée avec succès'
        ]);
    } else {
        throw new Exception('Erreur lors de la suppression');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}