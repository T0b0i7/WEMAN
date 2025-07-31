<?php
require_once '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO classes (nom, niveau, ordre, actif) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([
            $_POST['nom'],
            $_POST['niveau'],
            $_POST['ordre'],
            isset($_POST['actif']) ? 1 : 0
        ]);
        
        echo json_encode(['success' => $result]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}