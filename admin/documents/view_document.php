<?php
require_once '../../config/connexion.php';

$id = intval($_GET['id']);

try {
    $sql = "SELECT d.*, c.nom as nom_categorie 
            FROM documents d 
            LEFT JOIN categories_documents c ON d.categorie_id = c.id 
            WHERE d.id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        echo "Document non trouvé.";
        exit;
    }
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir Document - WEMANTCHE Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .document-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .document-details h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .document-details table {
            margin-bottom: 20px;
        }
        .document-details img {
            max-width: 200px;
            border-radius: 8px;
        }
        .btn-back {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="document-details">
            <h1 class="mb-4">Détails du Document</h1>
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td><?php echo htmlspecialchars($document['id']); ?></td>
                </tr>
                <tr>
                    <th>Titre</th>
                    <td><?php echo htmlspecialchars($document['titre']); ?></td>
                </tr>
                <tr>
                    <th>Contenu</th>
                    <td><?php echo nl2br(htmlspecialchars($document['contenu'])); ?></td>
                </tr>
                <tr>
                    <th>Catégorie</th>
                    <td><?php echo htmlspecialchars($document['nom_categorie']); ?></td>
                </tr>
                <tr>
                    <th>Prix</th>
                    <td><?php echo htmlspecialchars($document['prix']); ?> FCFA</td>
                </tr>
                <tr>
                    <th>Chemin du fichier</th>
                    <td><?php echo htmlspecialchars($document['file_path']); ?></td>
                </tr>
                <tr>
                    <th>Image</th>
                    <td>
                        <?php if(!empty($document['image'])): ?>
                            <img src="<?php echo htmlspecialchars($document['image']); ?>" alt="Image du document">
                        <?php else: ?>
                            Aucune image
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td><?php echo nl2br(htmlspecialchars($document['notes'])); ?></td>
                </tr>
                <tr>
                    <th>Langues</th>
                    <td><?php echo htmlspecialchars($document['langues']); ?></td>
                </tr>
                <tr>
                    <th>Niveau</th>
                    <td><?php echo htmlspecialchars($document['niveau']); ?></td>
                </tr>
                <tr>
                    <th>Mots Clés</th>
                    <td><?php echo nl2br(htmlspecialchars($document['mots_cles'])); ?></td>
                </tr>
                <tr>
                    <th>Taille du Fichier</th>
                    <td><?php echo htmlspecialchars($document['taille_fichier']); ?> octets</td>
                </tr>
                <tr>
                    <th>Type de Fichier</th>
                    <td><?php echo htmlspecialchars($document['type_fichier']); ?></td>
                </tr>
                <tr>
                    <th>Statut</th>
                    <td><?php echo htmlspecialchars($document['statut']); ?></td>
                </tr>
                <tr>
                    <th>Créé à</th>
                    <td><?php echo htmlspecialchars($document['cree_a']); ?></td>
                </tr>
                <tr>
                    <th>Mis à jour à</th>
                    <td><?php echo htmlspecialchars($document['mis_a_jour_a']); ?></td>
                </tr>
            </table>
            <a href="index.php" class="btn btn-secondary btn-back">Retour</a>
        </div>
    </div>
</body>
</html>