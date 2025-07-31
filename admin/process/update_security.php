<?php
require_once '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE settings SET 
            two_factor_auth = :two_factor_auth,
            pwd_min_length = :pwd_min_length,
            pwd_uppercase = :pwd_uppercase,
            pwd_special_char = :pwd_special_char
        ");
        
        $stmt->execute([
            'two_factor_auth' => isset($_POST['two_factor_auth']) ? 1 : 0,
            'pwd_min_length' => isset($_POST['pwd_min_length']) ? 1 : 0,
            'pwd_uppercase' => isset($_POST['pwd_uppercase']) ? 1 : 0,
            'pwd_special_char' => isset($_POST['pwd_special_char']) ? 1 : 0
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Paramètres de sécurité mis à jour']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
