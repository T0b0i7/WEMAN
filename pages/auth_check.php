<?php
// Inclure la connexion à la base de données
resession_start();
require_once '../config/connexion.php';

// Fonction pour vérifier si l'utilisateur est connecté et actif
function estUtilisateurActif($pdo) {
    // Vérifier si l'utilisateur est connecté (si l'ID utilisateur est en session)
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    try {
        // Récupérer le statut de l'utilisateur
        $stmt = $pdo->prepare("SELECT statut FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur existe et a le statut "actif"
        return ($user && $user['statut'] === 'actif');
    } catch (PDOException $e) {
        // Logger l'erreur en cas de problème
        error_log("Erreur lors de la vérification de l'utilisateur : " . $e->getMessage());
        return false;
    }
}
?>