<?php
require_once('../../config/connexion.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $pdo->beginTransaction();
        
        $id = (int)$_POST['id'];
        
        // Vérifier si le message existe
        $checkStmt = $pdo->prepare("SELECT id, statut FROM contacts WHERE id = :id");
        $checkStmt->execute(['id' => $id]);
        $message = $checkStmt->fetch();
        
        if (!$message) {
            throw new Exception('Message non trouvé');
        }

        // Supprimer le message
        $deleteStmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
        $success = $deleteStmt->execute(['id' => $id]);

        if (!$success) {
            throw new Exception('Erreur lors de la suppression');
        }

        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Message supprimé avec succès'
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Requête invalide'
    ]);
}