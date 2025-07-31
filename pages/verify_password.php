<?php
// filepath: c:\xampp\htdocs\WEMAN\pages\verify_password.php

// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once '../config/connexion.php';

// Vérifier si la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données JSON envoyées
    $data = json_decode(file_get_contents('php://input'), true);

    $password = $data['password'] ?? '';
    $documentId = $data['documentId'] ?? '';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour effectuer cette action.']);
        exit();
    }

    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT mot_de_passe_hash FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['mot_de_passe_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Le mot de passe que vous avez saisi est incorrect. Veuillez réessayer.']);
        exit();
    }

    // Si le mot de passe est correct
    echo json_encode(['success' => true]);
    exit();
}