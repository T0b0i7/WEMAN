<?php
require_once '../../config/connexion.php';
require_once 'notifications.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    markNotificationAsRead($_POST['id']);
    echo json_encode(['success' => true]);
}
