<?php
// Inclure le fichier de connexion PDO
require_once '../../config/connexion.php';

$document_id = $_GET['id'];

try {
    // Préparer la requête pour récupérer le document
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = :id");
    $stmt->bindParam(':id', $document_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        $file_path = '../../uploads/' . $document['fichier']; // Assurez-vous que le chemin est correct

        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            echo "Fichier non trouvé.";
        }
    } else {
        echo "Document non trouvé.";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>