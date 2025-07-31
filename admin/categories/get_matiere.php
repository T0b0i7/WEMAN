<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM matieres WHERE id = ?");
    $stmt->execute([$id]);
    $matiere = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$matiere) {
        echo json_encode([
            'success' => false,
            'message' => 'Matière non trouvée',
            'icon' => 'error',
            'title' => 'Erreur'
        ]);
        exit;
    }
    
    // Ajout des boutons d'action dans la réponse
    $matiere['actions'] = "
        <button onclick='editMatiere({$matiere['id']})' class='btn btn-warning btn-sm'>
            <i class='fas fa-edit'></i>
        </button>
        <button onclick='deleteMatiere({$matiere['id']})' class='btn btn-danger btn-sm'>
            <i class='fas fa-trash'></i>
        </button>
    ";
    
    echo json_encode([
        'success' => true,
        'data' => $matiere,
        'icon' => 'success',
        'title' => 'Succès'
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération : ' . $e->getMessage(),
        'icon' => 'error',
        'title' => 'Erreur'
    ]);
}
?>