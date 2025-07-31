<?php
session_start();
require_once '../config/connexion.php';

// Vérification améliorée de la connexion
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'utilisateur existe et est actif
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = ? AND statut = 'actif'");
$stmt->execute([$_SESSION['user_id']]);
if (!$stmt->fetch()) {
    // L'utilisateur n'existe pas ou n'est pas actif
    session_destroy();
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ancien_mdp = $_POST['ancien_mdp'];
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirmer_mdp = $_POST['confirmer_mdp'];
    
    // Vérifier l'ancien mot de passe
    $stmt = $pdo->prepare("SELECT mot_de_passe_hash FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (password_verify($ancien_mdp, $user['mot_de_passe_hash'])) {
        if ($nouveau_mdp === $confirmer_mdp) {
            // Mettre à jour le mot de passe
            $nouveau_mdp_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe_hash = ? WHERE id = ?");
            
            if ($stmt->execute([$nouveau_mdp_hash, $_SESSION['user_id']])) {
                $_SESSION['message'] = "Mot de passe modifié avec succès";
                header('Location: profile.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Les nouveaux mots de passe ne correspondent pas";
        }
    } else {
        $_SESSION['error'] = "Ancien mot de passe incorrect";
    }
}

header('Location: profile.php');
exit();
?>
