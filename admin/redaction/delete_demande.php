<?php
require_once '../../config/connexion.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM demandes_redaction WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute(['id' => $id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Erreur lors de la suppression']);
    }
}
?>
