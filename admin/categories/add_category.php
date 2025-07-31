<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        // Définir des valeurs par défaut si les champs sont vides
        $nom = !empty($data['name']) ? $data['name'] : 'RAS';
        $description = !empty($data['description']) ? $data['description'] : 'RAS';
        $statut = !empty($data['statut']) ? $data['statut'] : 'disponible';
        $active = isset($data['active']) ? $data['active'] : 1;

        $sql = "INSERT INTO categories_documents (nom, description, statut, active) 
                VALUES (:nom, :description, :statut, :active)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':statut' => $statut,
            ':active' => $active
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Catégorie ajoutée avec succès'
        ]);
    } else {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Méthode non autorisée'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur base de données: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>