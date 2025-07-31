<?php
// Afficher les erreurs PHP pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config/connexion.php'; // Inclure votre fichier de connexion

try {
    // Paramètres de pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    // Compte total des documents
    $countQuery = "SELECT COUNT(*) as total FROM documents";
    $countStmt = $pdo->query($countQuery);
    $totalDocs = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalDocs / $limit);

    // Requête principale avec pagination et tri
    $sql = "SELECT d.id, d.titre, c.nom AS categorie, d.prix, d.notes, d.langues, 
            d.niveau, d.mots_cles, d.taille_fichier, d.type_fichier, d.statut, d.image, d.file_path
            FROM documents d
            JOIN categories_documents c ON d.categorie_id = c.id
            ORDER BY d.id DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les résultats avec les métadonnées de pagination
    header('Content-Type: application/json');
    echo json_encode([
        'documents' => $documents,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalDocs,
            'itemsPerPage' => $limit
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>