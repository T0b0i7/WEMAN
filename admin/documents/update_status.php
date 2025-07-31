<!-- filepath: /C:/xampp/htdocs/WEMANTCHE/admin/documents/update_status.php -->
<?php
require_once '../../config/connexion.php';
$data = json_decode(file_get_contents('php://input'), true);
$statut = $data['statut'];

$sql = "UPDATE documents SET statut = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $statut, $document_id);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['message'] = $stmt->error;
}

if (move_uploaded_file($fileTmpPath, $dest_path)) {
    $message = 'Le fichier est téléchargé avec succès.';
    $image = ''; // Ajouter la logique pour gérer l'image si nécessaire

    $sql = "UPDATE documents SET titre = ?, contenu = ?, categorie_id = ?, utilisateur_id = ?, prix = ?, notes = ?, langues = ?, niveau = ?, mots_cles = ?, taille_fichier = ?, type_fichier = ?, statut = ?, image = ?, mis_a_jour_a = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiissssissi", $titre, $contenu, $categorie_id, $utilisateur_id, $prix, $notes, $langues, $niveau, $mots_cles, $taille_fichier, $type_fichier, $statut, $image, $document_id);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>