<?php
require_once('../config/connexion.php');

// Vérifier si l'export est demandé en CSV
$format = isset($_GET['format']) ? $_GET['format'] : 'html';

// Récupérer les statistiques
$stats = [];

// Documents et téléchargements
$stmt = $pdo->query("SELECT COUNT(*) as total FROM documents");
$stats['total_documents'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(downloads_count) as total FROM documents");
$stats['total_downloads'] = $stmt->fetch()['total'];

// Statistiques par type de fichier
$stmt = $pdo->query("
    SELECT type_fichier, COUNT(*) as total 
    FROM documents 
    GROUP BY type_fichier
");
$stats['types_fichiers'] = $stmt->fetchAll();

// Documents les plus téléchargés
$stmt = $pdo->query("
    SELECT titre, downloads_count 
    FROM documents 
    ORDER BY downloads_count DESC 
    LIMIT 5
");
$stats['top_downloads'] = $stmt->fetchAll();

// Si format CSV demandé
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="rapport_wemantche_'.date('Y-m-d').'.csv"');
    
    $output = fopen('php://output', 'w');
    
    // En-têtes CSV
    fputcsv($output, ['Type de statistique', 'Valeur']);
    
    // Données générales
    fputcsv($output, ['Total Documents', $stats['total_documents']]);
    fputcsv($output, ['Total Téléchargements', $stats['total_downloads']]);
    
    // Types de fichiers
    fputcsv($output, ['', '']); // Ligne vide
    fputcsv($output, ['Types de fichiers', 'Nombre']);
    foreach ($stats['types_fichiers'] as $type) {
        fputcsv($output, [$type['type_fichier'], $type['total']]);
    }
    
    fclose($output);
    exit;
}

// Sinon afficher en HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rapport WEMANTCHE - <?php echo date('d/m/Y'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .report-header { text-align: center; margin-bottom: 30px; }
        .stats-section { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .download-links { margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>Rapport Statistique WEMANTCHE</h1>
        <p>Généré le <?php echo date('d/m/Y à H:i'); ?></p>
    </div>

    <div class="download-links">
        <a href="?format=csv" class="btn">Télécharger en CSV</a>
    </div>

    <div class="stats-section">
        <h2>Statistiques Globales</h2>
        <table>
            <tr>
                <th>Total Documents</th>
                <td><?php echo number_format($stats['total_documents']); ?></td>
            </tr>
            <tr>
                <th>Total Téléchargements</th>
                <td><?php echo number_format($stats['total_downloads']); ?></td>
            </tr>
        </table>
    </div>

    <div class="stats-section">
        <h2>Types de Fichiers</h2>
        <table>
            <tr>
                <th>Type</th>
                <th>Nombre</th>
            </tr>
            <?php foreach($stats['types_fichiers'] as $type): ?>
            <tr>
                <td><?php echo htmlspecialchars($type['type_fichier']); ?></td>
                <td><?php echo number_format($type['total']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="stats-section">
        <h2>Documents les Plus Téléchargés</h2>
        <table>
            <tr>
                <th>Document</th>
                <th>Téléchargements</th>
            </tr>
            <?php foreach($stats['top_downloads'] as $doc): ?>
            <tr>
                <td><?php echo htmlspecialchars($doc['titre']); ?></td>
                <td><?php echo number_format($doc['downloads_count']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
