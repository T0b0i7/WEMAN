<?php
require_once '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE classes SET nom = ?, niveau = ?, ordre = ?, actif = ? WHERE id = ?");
        $result = $stmt->execute([
            $_POST['nom'],
            $_POST['niveau'],
            $_POST['ordre'],
            isset($_POST['actif']) ? 1 : 0,
            $_POST['id']
        ]);
        
        echo json_encode(['success' => $result]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}