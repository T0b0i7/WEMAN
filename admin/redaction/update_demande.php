<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/connexion.php';

try {
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        throw new Exception('Données manquantes');
    }
    
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $description = $_POST['description'] ?? '';
    $budget = $_POST['budget'] ?? 0;
    $delai = $_POST['delai'] ?? null;
    
    $sql = "UPDATE demandes_redaction SET 
            statut = ?,
            description = ?,
            budget = ?,
            delai_souhaite = ?,
            date_modification = NOW()
            WHERE id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $description, $budget, $delai, $id]);
    
    echo json_encode(['success' => true]);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
