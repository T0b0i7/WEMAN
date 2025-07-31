<?php
require_once('../../config/connexion.php');

header('Content-Type: application/json');

$categorie_id = $_GET['categorie_id'] ?? null;
$niveau_etude = $_GET['niveau_etude'] ?? null;

if (!$categorie_id || !$niveau_etude) {
    echo json_encode(['error' => 'Paramètres manquants']);
    exit;
}

try {
    // Vérifier uniquement la combinaison catégorie + niveau d'étude
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM categories_prix 
                          WHERE categorie_id = ? AND niveau_etude = ?");
    $stmt->execute([$categorie_id, $niveau_etude]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'exists' => $result['count'] > 0,
        'message' => $result['count'] > 0 ? 'Un tarif existe déjà pour cette catégorie et ce niveau d\'études' : null
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Erreur de base de données',
        'message' => 'Une erreur est survenue lors de la vérification'
    ]);
}