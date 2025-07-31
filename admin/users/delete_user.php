<?php
header('Content-Type: application/json');
require_once '../../config/connexion.php';

try {
    if (!isset($_POST['id'])) {
        throw new Exception('ID utilisateur manquant');
    }

    $id = intval($_POST['id']);
    
    // Début de la transaction
    $pdo->beginTransaction();
    
    try {
        // 1. Supprimer d'abord toutes les références dans les tables liées
        $stmt = $pdo->prepare("DELETE FROM reponses_questionnaire WHERE utilisateur_id = ?");
        $stmt->execute([$id]);
        
        // 2. Supprimer l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        
        // Valider la transaction
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Utilisateur supprimé définitivement'
        ]);
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la suppression : ' . $e->getMessage()
    ]);
}

$pdo = null;