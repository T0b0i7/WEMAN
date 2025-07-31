<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $id = $_POST['id'];
    $nom = trim($_POST['nom']);
    
    // Vérification si le nom existe déjà pour une autre matière
    $stmt = $pdo->prepare("SELECT id FROM matieres WHERE nom = ? AND id != ?");
    $stmt->execute([$nom, $id]);
    if($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Une matière avec ce nom existe déjà'
        ]);
        exit;
    }
    
    // Mise à jour de la matière
    $stmt = $pdo->prepare("UPDATE matieres SET nom = ? WHERE id = ?");
    $stmt->execute([$nom, $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Matière mise à jour avec succès'
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour : ' . $e->getMessage()
    ]);
}
?>