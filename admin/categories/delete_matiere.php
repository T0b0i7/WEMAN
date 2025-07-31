<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $id = $_POST['id'];
    
    // Suppression de la matière
    $stmt = $pdo->prepare("DELETE FROM matieres WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Matière supprimée avec succès'
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
    ]);
}
?>