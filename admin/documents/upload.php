<?php
require_once('../../config/connexion.php');
require_once('../../config/upload_config.php');
require_once('../../classes/UploadException.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Vérification de la session
        session_start();
        if (!isset($_SESSION['user_id'])) {
            throw new Exception(UPLOAD_ERROR_MESSAGES['SESSION_ERROR']);
        }

        // Vérification du fichier
        if (!isset($_FILES['document'])) {
            throw new UploadException(UPLOAD_ERR_NO_FILE);
        }

        if ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            throw new UploadException($_FILES['document']['error']);
        }

        $file = $_FILES['document'];
        
        // Validation complète du fichier
        $validation_results = validateFile($file);
        if (!empty($validation_results['errors'])) {
            throw new Exception(implode(", ", $validation_results['errors']));
        }

        // Préparation du fichier
        $file_info = prepareFileUpload($file);
        
        // Transaction base de données
        $pdo->beginTransaction();
        
        try {
            // Déplacement du fichier
            if (!move_uploaded_file($file['tmp_name'], $file_info['full_path'])) {
                throw new Exception(UPLOAD_ERROR_MESSAGES['MOVE_ERROR']);
            }

            // Insertion en base de données sans utilisateur_id
            $stmt = $pdo->prepare(  "
                INSERT INTO documents (
                    titre, contenu, categorie_id, prix,
                    file_path, type_fichier, taille_fichier, langues, niveau,
                    mots_cles, statut
                ) VALUES (
                    :titre, :contenu, :categorie_id, :prix,
                    :file_path, :type_fichier, :taille_fichier, :langues, :niveau,
                    :mots_cles, 'en_attente'
                )
            ");

            $stmt->execute([
                'titre' => $_POST['titre'],
                'contenu' => $_POST['description'],
                'categorie_id' => $_POST['categorie_id'],
                'prix' => $_POST['prix'],
                'file_path' => $file_info['relative_path'],
                'type_fichier' => $file['type'],
                'taille_fichier' => $file['size'],
                'langues' => $_POST['langues'] ?? 'fr',
                'niveau' => $_POST['niveau'] ?? 'debutant',
                'mots_cles' => $_POST['mots_cles'] ?? ''
            ]);

            $document_id = $pdo->lastInsertId();

            // Enregistrer l'activité séparément
            logActivity($pdo, $_SESSION['user_id'], 'upload', $document_id);

            $pdo->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Document téléversé avec succès',
                'file_info' => [
                    'name' => $file['name'],
                    'size' => getHumanFileSize($file['size']),
                    'type' => $file['type']
                ]
            ]);

        } catch (Exception $e) {
            $pdo->rollBack();
            if (file_exists($file_info['full_path'])) {
                unlink($file_info['full_path']);
            }
            throw $e;
        }

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
    }
}

function validateFile($file) {
    $errors = [];
    
    // Vérification du type MIME
    if (!in_array($file['type'], ALLOWED_TYPES)) {
        $errors[] = UPLOAD_ERROR_MESSAGES['INVALID_TYPE'];
    }

    // Vérification de la taille
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = sprintf(
            "%s (Taille: %s, Max: %s)", 
            UPLOAD_ERROR_MESSAGES['FILE_TOO_LARGE'],
            getHumanFileSize($file['size']),
            getHumanFileSize(MAX_FILE_SIZE)
        );
    }

    return ['errors' => $errors];
}

function prepareFileUpload($file) {
    // Créer le dossier si nécessaire
    if (!is_dir(UPLOAD_PATH)) {
        if (!mkdir(UPLOAD_PATH, 0777, true)) {
            throw new Exception(UPLOAD_ERROR_MESSAGES['DIRECTORY_ERROR']);
        }
    }

    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid() . '_' . time() . '.' . $extension;
    
    return [
        'full_path' => UPLOAD_PATH . $newFileName,
        'relative_path' => 'uploads/documents/' . $newFileName
    ];
}

function logActivity($pdo, $user_id, $action, $document_id) {
    $stmt = $pdo->prepare("
        INSERT INTO activites (utilisateur_id, document_id, action, statut)
        VALUES (:user_id, :document_id, :action, 'success')
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'document_id' => $document_id,
        'action' => $action
    ]);
}
