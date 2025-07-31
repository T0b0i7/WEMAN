<?php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validation des champs requis
        $required_fields = ['prenom', 'nom', 'email', 'telephone', 'role', 'mot_de_passe'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ $field est requis");
            }
        }

        // Nettoyage et validation des données
        $prenom = filter_var($_POST['prenom'], FILTER_SANITIZE_STRING);
        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $telephone = filter_var($_POST['telephone'], FILTER_SANITIZE_STRING);
        $role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $statut = 'actif';

        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide");
        }

        // Vérification de l'existence de l'email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Cet email est déjà utilisé");
        }

        // Préparation et exécution de la requête d'insertion
        $sql = "INSERT INTO utilisateurs (prenom, nom, email, telephone, role, mot_de_passe_hash, statut) 
                VALUES (:prenom, :nom, :email, :telephone, :role, :mot_de_passe, :statut)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':role' => $role,
            ':mot_de_passe' => $mot_de_passe,
            ':statut' => $statut
        ]);

        if ($result) {
            // Récupération de l'ID inséré
            $userId = $pdo->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur ajouté avec succès',
                'userId' => $userId
            ]);
        } else {
            throw new Exception("Erreur lors de l'insertion dans la base de données");
        }
    } else {
        throw new Exception("Méthode non autorisée");
    }

} catch (PDOException $e) {
    error_log("Erreur PDO lors de l'ajout d'utilisateur: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => "Erreur de base de données: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Erreur lors de l'ajout d'utilisateur: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
