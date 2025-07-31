<?php
require_once '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Variables pour les chemins des fichiers
        $uploadDir = '../../uploads/documents/';
        $imageDir = '../../uploads/images/';
        
        // Créer les répertoires s'ils n'existent pas
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        if (!file_exists($imageDir)) mkdir($imageDir, 0777, true);

        // Traitement du fichier document
        $file_path = '';
        if (isset($_FILES['documentFile']) && $_FILES['documentFile']['error'] === UPLOAD_ERR_OK) {
            $uniqueName = uniqid() . '_' . basename($_FILES['documentFile']['name']);
            $file_path = $uploadDir . $uniqueName;
            
            if (!move_uploaded_file($_FILES['documentFile']['tmp_name'], $file_path)) {
                throw new Exception('Échec du téléversement du fichier');
            }
        } else {
            throw new Exception('Aucun fichier téléversé');
        }

        // Génération de l'image de prévisualisation
        $image_path = null;
        if (isset($_FILES['documentFile'])) {
            $file_extension = strtolower(pathinfo($_FILES['documentFile']['name'], PATHINFO_EXTENSION));
            
            if ($file_extension === 'pdf') {
                // Pour les PDF, utiliser une image par défaut
                $image_path = '../../assets/images/default-pdf.jpg';
            } elseif (in_array($file_extension, ['jpg', 'jpeg', 'png'])) {
                // Pour les images, copier directement le fichier
                // Utilisation de Imagick pour la prévisualisation PDF
                try {
                    $im = new Imagick();
                    $im->setResolution(300, 300);
                    $im->readImage($_FILES['documentFile']['tmp_name'] . '[0]'); // Première page
                    $im->setImageFormat('jpg');
                    $im->writeImage($image_path);
                    $im->clear();
                    $im->destroy();
                } catch (Exception $e) {
                    // Si échec de génération de prévisualisation, utiliser une image par défaut
                    $image_path = '../../assets/images/default-pdf.jpg';
                }
            } elseif (in_array($file_extension, ['jpg', 'jpeg', 'png'])) {
                // Pour les images, copier directement le fichier
                $image_name = uniqid() . '_preview.' . $file_extension;
                $image_path = $imageDir . $image_name;
                copy($_FILES['documentFile']['tmp_name'], $image_path);
            } else {
                // Pour les autres types de fichiers, utiliser une image par défaut
                $image_path = '../../assets/images/default-doc.jpg';
            }
        }

        // Préparation de l'insertion en base de données
        $sql = "INSERT INTO documents (
            titre, contenu, categorie_id, prix,
            file_path, image, notes, langues, niveau,
            mots_cles, taille_fichier, type_fichier, statut
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['category'],
            $_POST['price'],
            $file_path,
            $image_path, // Chemin de l'image de prévisualisation
            $_POST['notes'],
            $_POST['language'],
            $_POST['level'],
            $_POST['keywords'],
            $_FILES['documentFile']['size'], // Taille réelle du fichier en octets
            $_FILES['documentFile']['type'], // MIME type du fichier
            $_POST['status']
        ]);

        if ($result) {
            header('Location: upload.html?uploadSuccess=true');
        } else {
            throw new Exception('Erreur lors de l\'insertion en base de données');
        }

    } catch (Exception $e) {
        // En cas d'erreur, supprimer les fichiers téléversés si nécessaire
        if (isset($file_path) && file_exists($file_path)) {
            unlink($file_path);
        }
        if (isset($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
        
        header('Location: upload.html?uploadSuccess=false&error=' . urlencode($e->getMessage()));
    }
    exit();
}
?>