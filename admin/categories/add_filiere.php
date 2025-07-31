<?php
require_once '../../config/connexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO filieres (nom, departement, niveau, actif) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([
            $_POST['nom'],
            $_POST['departement'],
            $_POST['niveau'],
            isset($_POST['actif']) ? 1 : 0
        ]);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Erreur lors de l\'ajout de la filière');
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de base de données : ' . $e->getMessage()
        ]);
    }
}