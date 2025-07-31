<?php
require_once('../../config/connexion.php');
require_once('../../includes/notifications.php');

function updateOldMessages() {
    global $pdo;
    
    try {
        $pdo->beginTransaction();

        // Mise à jour des messages de plus de 48h
        $updateQuery = "
            UPDATE contacts 
            SET statut = 'termine' 
            WHERE statut = 'nouvelle' 
            AND TIMESTAMPDIFF(HOUR, date_creation, NOW()) >= 48";

        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute();
        
        $updated = $stmt->rowCount();
        
        $pdo->commit();
        
        // Récupérer les messages mis à jour pour envoyer les notifications
        $updatedMessages = $pdo->query("
            SELECT * FROM contacts 
            WHERE statut = 'termine' 
            AND TIMESTAMPDIFF(HOUR, date_creation, NOW()) >= 48
        ")->fetchAll();
        
        foreach ($updatedMessages as $message) {
            // Envoyer une notification à l'administrateur
            $notifMessage = "Le message de {$message['prenom']} {$message['nom']} a été marqué comme terminé après 48h.";
            createNotification('admin', 'message_termine', $notifMessage);
            
            // Log de l'action
            error_log("Statut mis à jour pour le message ID: {$message['id']}");
        }
        
        return count($updatedMessages);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Erreur mise à jour statuts: " . $e->getMessage());
        return false;
    }
}