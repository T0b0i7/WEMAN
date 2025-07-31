<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $nom = trim($_POST['nom']);
    
    // Vérification si la matière existe déjà
    $stmt = $pdo->prepare("SELECT id FROM matieres WHERE nom = ?");
    $stmt->execute([$nom]);
    if($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cette matière existe déjà'
        ]);
        exit;
    }
    
    // Ajout de la nouvelle matière
    $stmt = $pdo->prepare("INSERT INTO matieres (nom) VALUES (?)");
    $stmt->execute([$nom]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Matière ajoutée avec succès'
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'ajout : ' . $e->getMessage()
    ]);
}
?>