<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit();
}

// Inclure la connexion à la base de données
require_once '../config/connexion.php';

// Récupérer l'ID du document
if (isset($_GET['id'])) {
    $documentId = intval($_GET['id']);

    // Rechercher le document dans la base de données
    $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id = :id");
    $stmt->execute([':id' => $documentId]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document) {
        // Correction : Ajout de la barre oblique manquante
        $filePath = '../uploads/documents/' . $document['file_path'];

        if (file_exists($filePath)) {
            // Forcer le téléchargement
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit();
        } else {
            echo "Fichier introuvable.";
        }
    } else {
        echo "Document introuvable.";
    }
} else {
    echo "ID de document non spécifié.";
}
?>