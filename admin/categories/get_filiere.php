<?php
require_once '../../config/connexion.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM filieres WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $filiere = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($filiere) {
            echo json_encode([
                'success' => true,
                'data' => $filiere
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Filière non trouvée'
            ]);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur : ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID non fourni'
    ]);
}