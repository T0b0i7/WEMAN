<?php
require_once '../../config/connexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Récupération et validation des données
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $departement = isset($_POST['departement']) ? trim($_POST['departement']) : '';
        $niveau = isset($_POST['niveau']) ? trim($_POST['niveau']) : '';
        $actif = isset($_POST['actif']) ? 1 : 0;

        // Validation basique
        if (empty($id) || empty($nom) || empty($departement) || empty($niveau)) {
            throw new Exception('Tous les champs obligatoires doivent être remplis');
        }

        // Vérification de l'existence de la filière
        $checkStmt = $pdo->prepare("SELECT id FROM filieres WHERE id = ?");
        $checkStmt->execute([$id]);
        
        if (!$checkStmt->fetch()) {
            throw new Exception('Filière non trouvée');
        }

        // Mise à jour
        $stmt = $pdo->prepare("
            UPDATE filieres 
            SET nom = :nom, 
                departement = :departement, 
                niveau = :niveau, 
                actif = :actif 
            WHERE id = :id
        ");

        $result = $stmt->execute([
            ':nom' => $nom,
            ':departement' => $departement,
            ':niveau' => $niveau,
            ':actif' => $actif,
            ':id' => $id
        ]);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Filière mise à jour avec succès'
            ]);
        } else {
            throw new Exception('Erreur lors de la mise à jour');
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}