<?php
require_once '../../config/connexion.php';

function createNotification($user_id, $message, $type = 'info') {
    global $pdo;
    try {
        $sql = "INSERT INTO notifications (user_id, message, type, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $message, $type]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

function getUnreadNotifications($user_id) {
    global $pdo;
    try {
        $sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

function markNotificationAsRead($notification_id) {
    global $pdo;
    try {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$notification_id]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}
