<?php
require_once '../../config/connexion.php';
$document_id = $_GET['id'];
$sql = "SELECT * FROM documents WHERE id = $document_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
} else {
    echo "Document non trouvé.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Document - WEMANTCHE Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Modifier Document</h1>
        <form action="update_document.php" method="post">
            <input type="hidden" name="id" value="<?php echo $document['id']; ?>">
            <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $document['titre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu</label>
                <textarea class="form-control" id="contenu" name="contenu" rows="5" required><?php echo $document['contenu']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="categorie_id" class="form-label">Catégorie</label>
                <input type="text" class="form-control" id="categorie_id" name="categorie_id" value="<?php echo $document['categorie_id']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="prix" class="form-label">Prix</label>
                <input type="number" class="form-control" id="prix" name="prix" value="<?php echo $document['prix']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $document['notes']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="langues" class="form-label">Langues</label>
                <input type="text" class="form-control" id="langues" name="langues" value="<?php echo $document['langues']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="niveau" class="form-label">Niveau</label>
                <input type="text" class="form-control" id="niveau" name="niveau" value="<?php echo $document['niveau']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="mots_cles" class="form-label">Mots Clés</label>
                <input type="text" class="form-control" id="mots_cles" name="mots_cles" value="<?php echo $document['mots_cles']; ?>">
            </div>
            <div class="mb-3">
                <label for="taille_fichier" class="form-label">Taille du Fichier</label>
                <input type="number" class="form-control" id="taille_fichier" name="taille_fichier" value="<?php echo $document['taille_fichier']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="type_fichier" class="form-label">Type de Fichier</label>
                <input type="text" class="form-control" id="type_fichier" name="type_fichier" value="<?php echo $document['type_fichier']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
        <a href="index.php" class="btn btn-secondary mt-3">Retour</a>
    </div>
</body>
</html>