<?php
header('Content-Type: application/json');
session_start();

require_once '../config/connexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || $_POST['action'] !== 'creer') {
        throw new Exception('Requête invalide');
    }

    // Validation des champs requis
    $required_fields = ['categorie_id', 'sujet', 'description', 'delai_souhaite', 'budget'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Le champ $field est requis");
        }
    }

    // Traitement des fichiers uploadés
    $documents_reference = '';
    if (!empty($_FILES['documents']['name'][0])) {
        // Modification du chemin pour le dossier uploads
        $upload_dir = '../uploads/documents/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $uploaded_files = [];
        foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
            $filename = uniqid() . '_' . $_FILES['documents']['name'][$key];
            $filepath = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $filepath)) {
                $uploaded_files[] = $filename;
            }
        }
        $documents_reference = implode(',', $uploaded_files);
    }

    // Requête d'insertion
    $sql = "INSERT INTO demandes_redaction (
        utilisateur_id, 
        categorie_id, 
        sujet, 
        description, 
        delai_souhaite, 
        budget, 
        documents_reference,
        statut
    ) VALUES (
        :utilisateur_id,
        :categorie_id,
        :sujet,
        :description,
        :delai_souhaite,
        :budget,
        :documents_reference,
        'en_attente'
    )";

    $params = [
        ':utilisateur_id' => $_SESSION['user_id'] ?? 1,
        ':categorie_id' => $_POST['categorie_id'],
        ':sujet' => $_POST['sujet'],
        ':description' => $_POST['description'],
        ':delai_souhaite' => $_POST['delai_souhaite'],
        ':budget' => $_POST['budget'],
        ':documents_reference' => $documents_reference
    ];

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute($params)) {
        echo json_encode([
            'success' => true,
            'message' => 'Votre demande a été enregistrée avec succès!'
        ]);
    } else {
        throw new Exception('Erreur lors de l\'enregistrement de la demande');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>