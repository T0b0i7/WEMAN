<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Vérifier si le nom existe déjà pour une autre catégorie
        $check = $pdo->prepare("SELECT id FROM categories_documents WHERE nom = :nom AND id != :id");
        $check->execute([
            ':nom' => $data['name'],
            ':id' => $data['id']
        ]);
        
        if ($check->rowCount() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Une catégorie avec ce nom existe déjà'
            ]);
            exit;
        }

        // Mettre à jour la catégorie
        $stmt = $pdo->prepare("UPDATE categories_documents SET 
            nom = :nom,
            description = :description,
            statut = :statut,
            active = :active
            WHERE id = :id");

        $stmt->execute([
            ':id' => $data['id'],
            ':nom' => $data['name'],
            ':description' => $data['description'],
            ':statut' => $data['statut'],
            ':active' => $data['active']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Catégorie mise à jour avec succès'
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur base de données: ' . $e->getMessage()
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories_documents WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category) {
            echo json_encode($category);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Catégorie non trouvée'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur base de données: ' . $e->getMessage()
        ]);
    }
}
?>