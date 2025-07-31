<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $id = intval($_GET['id']);
    
    $sql = "SELECT id, prenom, nom, email, telephone, role, statut, cree_a, mis_a_jour_a 
            FROM utilisateurs 
            WHERE id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Utilisateur non trouvé']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit(); // Arrête l'exécution du script après avoir envoyé le JSON