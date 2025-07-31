<?php
require_once '../../config/connexion.php';

if (!isset($_GET['id'])) {
    die(json_encode(['error' => 'ID manquant']));
}

$id = intval($_GET['id']);

try {
    $sql = "SELECT u.email 
            FROM demandes_redaction dr 
            LEFT JOIN utilisateurs u ON dr.utilisateur_id = u.id 
            WHERE dr.id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['email' => $result['email'] ?? null]);
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
