<?php
require_once '../../config/connexion.php';

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $classe = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($classe) {
            echo json_encode($classe);
        } else {
            echo json_encode(['error' => 'Classe non trouvée']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID non fourni']);
}