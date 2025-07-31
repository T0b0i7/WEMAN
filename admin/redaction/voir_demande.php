<?php
require_once '../../config/connexion.php';

if (!isset($_GET['id'])) {
    header('Location: demandes.php');
    exit;
}

$id = intval($_GET['id']);

try {
    // Modifier la requête SQL
    $sql = "SELECT 
    dr.*,
    u.nom, 
    u.prenom, 
    cd.nom as categorie_nom 
FROM demandes_redaction dr 
LEFT JOIN utilisateurs u ON dr.utilisateur_id = u.id 
LEFT JOIN categories_documents cd ON dr.categorie_id = cd.id 
WHERE dr.id = ?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$demande) {
        header('Location: demandes.php');
        exit;
    }
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la Demande - WEMANTCHE Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="../../assets/css/admin.css" rel="stylesheet">
    <style>
        .table th {
            background-color: rgba(0,0,0,0.03);
            font-weight: 600;
        }
        .content-section {
            background-color: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        /* Style pour le contenu des sections */
        .content-section .bg-light {
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            min-height: 100px;
            border: 1px solid #dee2e6;
        }

        /* Style pour les titres des sections */
        .content-section h5 {
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        /* Style pour la zone de contenu */
        .content-details {
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Ajout d'une bordure colorée selon le type de contenu */
        .content-section.objectifs .bg-light {
            border-left: 4px solid #3498db;
        }

        .content-section.plan .bg-light {
            border-left: 4px solid #2ecc71;
        }

        .content-section.consignes .bg-light {
            border-left: 4px solid #e74c3c;
        }

        .content-section.description .bg-light {
            border-left: 4px solid #f1c40f;
        }
    </style>
</head>
<body class="bg-dark">
    <div class="container py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Détails de la Demande #<?= $demande['id'] ?></h3>
                <a href="demandes.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informations client -->
                    <div class="col-md-6 mb-4">
                        <h4 class="text-primary">Informations Client</h4>
                        <table class="table">
                            <tr>
                                <th>ID Demande</th>
                                <td>#<?= htmlspecialchars($demande['id']) ?></td>
                            </tr>
                            <tr>
                                <th>Utilisateur</th>
                                <td><?= htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']) ?></td>
                            </tr>
                            <tr>
                                <th>ID Utilisateur</th>
                                <td>#<?= htmlspecialchars($demande['utilisateur_id']) ?></td>
                            </tr>
                            <tr>
                                <th>Catégorie</th>
                                <td><?= htmlspecialchars($demande['categorie_nom']) ?> (#<?= htmlspecialchars($demande['categorie_id']) ?>)</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Détails académiques -->
                    <div class="col-md-6 mb-4">
                        <h4 class="text-primary">Détails Académiques</h4>
                        <table class="table">
                            <tr>
                                <th>Filière</th>
                                <td><?= $demande['filiere'] ? htmlspecialchars($demande['filiere']) : '-' ?></td>
                            </tr>
                            <tr>
                                <th>Classe</th>
                                <td><?= $demande['classe'] ? htmlspecialchars($demande['classe']) : '-' ?></td>
                            </tr>
                            <tr>
                                <th>Matière</th>
                                <td><?= $demande['matiere'] ? htmlspecialchars($demande['matiere']) : '-' ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Informations sur la demande -->
                    <div class="col-12 mb-4">
                        <h4 class="text-primary">Informations sur la Demande</h4>
                        <table class="table">
                            <tr>
                                <th width="20%">Sujet/Thème</th>
                                <td><?= nl2br(htmlspecialchars($demande['sujet_theme'])) ?></td>
                            </tr>
                            <tr>
                                <th>Budget</th>
                                <td><?= number_format($demande['budget'], 0, ',', ' ') ?> FCFA</td>
                            </tr>
                            <tr>
                                <th>Statut</th>
                                <td>
                                    <span class="badge bg-<?= getStatusClass($demande['statut']) ?>">
                                        <?= formatStatus($demande['statut']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Délai souhaité</th>
                                <td><?= formatDate($demande['delai_souhaite']) ?></td>
                            </tr>
                            <tr>
                                <th>Date de création</th>
                                <td><?= formatDate($demande['date_creation']) ?></td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td><?= formatDate($demande['date_modification']) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Contenu détaillé -->
                    <div class="col-12">
                        <h4 class="text-primary mb-4">Contenu Détaillé</h4>
                        
                        <div class="content-section description mb-4">
                            <h5><i class="fas fa-info-circle me-2"></i>Description générale</h5>
                            <div class="p-3 bg-light rounded content-details">
                                <?= nl2br(htmlspecialchars($demande['description'])) ?: '<em class="text-muted">Non spécifié</em>' ?>
                            </div>
                        </div>

                        <div class="content-section objectifs mb-4">
                            <h5><i class="fas fa-bullseye me-2"></i>Objectifs</h5>
                            <div class="p-3 bg-light rounded content-details">
                                <?= nl2br(htmlspecialchars($demande['objectifs'])) ?: '<em class="text-muted">Non spécifié</em>' ?>
                            </div>
                        </div>

                        <div class="content-section plan mb-4">
                            <h5><i class="fas fa-list-ul me-2"></i>Plan souhaité</h5>
                            <div class="p-3 bg-light rounded content-details">
                                <?= nl2br(htmlspecialchars($demande['plan_souhaite'])) ?: '<em class="text-muted">Non spécifié</em>' ?>
                            </div>
                        </div>

                        <div class="content-section consignes">
                            <h5><i class="fas fa-tasks me-2"></i>Consignes spécifiques</h5>
                            <div class="p-3 bg-light rounded content-details">
                                <?= nl2br(htmlspecialchars($demande['consignes_specifiques'])) ?: '<em class="text-muted">Non spécifié</em>' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    function getStatusClass($status) {
        $classes = [
            'en_attente' => 'warning',
            'en_cours' => 'info',
            'termine' => 'success',
            'annule' => 'danger'
        ];
        return $classes[$status] ?? 'secondary';
    }

    function formatStatus($status) {
        return ucfirst(str_replace('_', ' ', $status));
    }
    ?>
</body>
</html>