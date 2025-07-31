<?php
require_once '../../config/connexion.php';

$id = $_GET['id'];

$sql = "SELECT id, titre, contenu, categorie_id, prix, niveau, langues, mots_cles, publie, notes, taille_fichier, type_fichier, statut FROM documents WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$document = $result->fetch_assoc();

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($document);
?>