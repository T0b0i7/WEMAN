<?php
function securePage() {
    session_start();
    if (!isset($_SESSION['user'])) {
        header('Location: /WEMAN/pages/login.php');
        exit();
    }
}

function adminOnly() {
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: /WEMAN/index.php');
        exit();
    }
}

// Fonction pour nettoyer les entrées 
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
