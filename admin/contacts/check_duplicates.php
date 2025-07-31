<?php
require_once('../../config/connexion.php');

function checkAndRemoveDuplicates() {
    global $pdo;
    
    try {
        // Trouver les doublons basés sur email et message
        $query = "SELECT email, message, COUNT(*) as count, MIN(id) as keep_id
                 FROM contacts 
                 GROUP BY email, message 
                 HAVING COUNT(*) > 1";
                 
        $stmt = $pdo->query($query);
        $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($duplicates as $duplicate) {
            // Supprimer tous les doublons sauf le plus ancien
            $deleteQuery = "DELETE FROM contacts 
                          WHERE email = :email 
                          AND message = :message 
                          AND id != :keep_id";
                          
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->execute([
                'email' => $duplicate['email'],
                'message' => $duplicate['message'],
                'keep_id' => $duplicate['keep_id']
            ]);
            
            // Log les suppressions
            error_log("Doublons supprimés pour l'email: {$duplicate['email']}");
        }
        
        return count($duplicates);
    } catch (Exception $e) {
        error_log("Erreur lors de la vérification des doublons: " . $e->getMessage());
        return false;
    }
}