<?php
require_once '../../config/connexion.php';

try {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM documents WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $response = array('success' => true);
    } else {
        $response = array('success' => false);
    }
} catch(PDOException $e) {
    $response = array(
        'success' => false,
        'message' => "Erreur : " . $e->getMessage()
    );
}

header('Content-Type: application/json');
echo json_encode($response);
?>