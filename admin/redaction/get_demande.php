<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../config/connexion.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID manquant');
    }
    
    $id = intval($_GET['id']);
    
    $sql = "SELECT dr.*, u.nom, u.prenom, cd.nom as categorie_nom 
            FROM demandes_redaction dr 
            LEFT JOIN utilisateurs u ON dr.utilisateur_id = u.id 
            LEFT JOIN categories_documents cd ON dr.categorie_id = cd.id 
            WHERE dr.id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$demande) {
        throw new Exception('Demande non trouvée');
    }
    
    echo json_encode($demande, JSON_UNESCAPED_UNICODE);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
