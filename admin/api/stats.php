<?php
require_once('../../config/connexion.php');

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch($action) {
    case 'documents_stats':
        $periode = $_GET['periode'] ?? '7';
        $stmt = $pdo->prepare("
            SELECT 
                DATE(cree_a) as date, 
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'disponible' THEN 1 ELSE 0 END) as disponibles,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente
            FROM documents 
            WHERE cree_a >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
            GROUP BY DATE(cree_a)
            ORDER BY date ASC
        ");
        $stmt->execute([$periode]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si aucun résultat, ajouter des données fictives pour démonstration
        if (empty($result)) {
            $result = [
                ['date' => date('Y-m-d'), 'total' => 15, 'disponibles' => 10, 'en_attente' => 5],
                ['date' => date('Y-m-d', strtotime('-1 day')), 'total' => 12, 'disponibles' => 8, 'en_attente' => 4],
                ['date' => date('Y-m-d', strtotime('-2 day')), 'total' => 18, 'disponibles' => 15, 'en_attente' => 3],
                ['date' => date('Y-m-d', strtotime('-3 day')), 'total' => 11, 'disponibles' => 7, 'en_attente' => 4],
                ['date' => date('Y-m-d', strtotime('-4 day')), 'total' => 14, 'disponibles' => 11, 'en_attente' => 3],
                ['date' => date('Y-m-d', strtotime('-5 day')), 'total' => 16, 'disponibles' => 13, 'en_attente' => 3],
                ['date' => date('Y-m-d', strtotime('-6 day')), 'total' => 13, 'disponibles' => 10, 'en_attente' => 3]
            ];
        }
        
        echo json_encode($result);
        break;

    case 'document_types':
        $stmt = $pdo->query("
            SELECT 
                cd.nom as categorie,
                COUNT(d.id) as total,
                SUM(d.prix) as revenu_total
            FROM categories_documents cd
            LEFT JOIN documents d ON cd.id = d.categorie_id
            WHERE cd.active = 1
            GROUP BY cd.id, cd.nom
            ORDER BY total DESC
        ");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Si aucun résultat, ajouter des données fictives
        if (empty($result)) {
            $result = [
                ['categorie' => 'Mémoires', 'total' => 45, 'revenu_total' => 675000],
                ['categorie' => 'Rapports', 'total' => 30, 'revenu_total' => 240000],
                ['categorie' => 'CV', 'total' => 25, 'revenu_total' => 125000],
                ['categorie' => 'Exposés', 'total' => 20, 'revenu_total' => 160000],
                ['categorie' => 'Autres', 'total' => 15, 'revenu_total' => 75000]
            ];
        }
        
        echo json_encode($result);
        break;
}
