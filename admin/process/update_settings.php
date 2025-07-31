<?php
require_once '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $default_language = mysqli_real_escape_string($conn, $_POST['default_language']);
    $timezone = mysqli_real_escape_string($conn, $_POST['timezone']);

    // Mettre à jour les paramètres
    $query = "UPDATE site_settings SET 
              site_name = '$site_name',
              contact_email = '$contact_email',
              description = '$description',
              default_language = '$default_language',
              timezone = '$timezone'";

    if ($conn->query($query)) {
        $_SESSION['success'] = "Les paramètres ont été mis à jour avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour des paramètres : " . $conn->error;
    }
}

header('Location: ../settings.php');
exit;
