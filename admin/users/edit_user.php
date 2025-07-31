<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Vérification si la requête est bien en POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    // Validation de l'ID
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('ID utilisateur invalide');
    }

    // Validation des données reçues
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);

    if (!$prenom || !$nom || !$email || !$telephone || !$role || !$statut) {
        throw new Exception('Tous les champs sont obligatoires');
    }

    // Vérification si l'utilisateur existe
    $checkStmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = ?");
    $checkStmt->execute([$id]);
    if (!$checkStmt->fetch()) {
        throw new Exception('Utilisateur non trouvé');
    }

    // Vérification si l'email existe déjà pour un autre utilisateur
    $emailCheckStmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
    $emailCheckStmt->execute([$email, $id]);
    if ($emailCheckStmt->fetch()) {
        throw new Exception('Cette adresse email est déjà utilisée');
    }

    // Mise à jour de l'utilisateur
    $sql = "UPDATE utilisateurs SET 
            prenom = :prenom,
            nom = :nom,
            email = :email,
            telephone = :telephone,
            role = :role,
            statut = :statut,
            mis_a_jour_a = CURRENT_TIMESTAMP
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        'id' => $id,
        'prenom' => $prenom,
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'role' => $role,
        'statut' => $statut
    ]);

    if (!$success) {
        throw new Exception('Erreur lors de la mise à jour');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Utilisateur mis à jour avec succès'
    ]);

} catch (Exception $e) {
    error_log('Erreur dans edit_user.php : ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
