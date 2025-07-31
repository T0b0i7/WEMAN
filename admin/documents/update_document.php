<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Configuration des répertoires
    $document_dir = '../../uploads/documents/';
    $image_dir = '../../uploads/images/';
    
    if (!file_exists($document_dir)) mkdir($document_dir, 0777, true);
    if (!file_exists($image_dir)) mkdir($image_dir, 0777, true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $titre = $_POST['title'];
        $contenu = $_POST['description'];
        $categorie_id = $_POST['category'];
        $prix = $_POST['price'];
        $notes = $_POST['notes'];
        $langues = $_POST['language'];
        $niveau = $_POST['level'];
        $mots_cles = $_POST['keywords'];
        $taille_fichier = $_POST['taille_fichier'];
        $type_fichier = $_POST['type_fichier'];
        $statut = $_POST['status'];

        // Traitement du document
        $doc_file = $_FILES['documentFile'];
        $doc_extension = strtolower(pathinfo($doc_file['name'], PATHINFO_EXTENSION));
        $doc_filename = uniqid() . '.' . $doc_extension;
        $file_path = $document_dir . $doc_filename;
        
        // Générer une image de prévisualisation pour les PDF
        $image = null;
        if ($doc_extension === 'pdf') {
            $image_filename = uniqid() . '.jpg';
            $image = $image_dir . $image_filename;
            // Ici vous pouvez ajouter le code pour générer la prévisualisation du PDF
        }

        if (move_uploaded_file($doc_file['tmp_name'], $file_path)) {
            $sql = "INSERT INTO documents (
                titre, contenu, categorie_id, prix,
                file_path, image, notes, langues, niveau,
                mots_cles, taille_fichier, type_fichier, statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssiidssssssss",
                $_POST['title'],
                $_POST['description'],
                $_POST['category'],
                $_POST['price'],
                $file_path,
                $image,
                $_POST['notes'],
                $_POST['language'],
                $_POST['level'],
                $_POST['keywords'],
                $_FILES['documentFile']['size'],
                $doc_extension,
                $_POST['status']
            );

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Erreur lors de l'insertion dans la base de données");
            }
        }
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
