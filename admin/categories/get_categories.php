<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT id, nom, description, statut, active, cree_a, mis_a_jour_a FROM categories_documents");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categories);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>