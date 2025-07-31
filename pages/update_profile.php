<?php
require_once '../config/connexion.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $password = $_POST['password'];
        $userId = $_SESSION['user_id'];

        // Vérifiez le mot de passe
        $stmt = $conn->prepare("SELECT mot_de_passe_hash FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $hashedPassword = $stmt->fetchColumn();

        if (password_verify($password, $hashedPassword)) {
            // Mettre à jour les informations de l'utilisateur
            $stmt = $conn->prepare("UPDATE utilisateurs SET prenom = :prenom, nom = :nom, email = :email, telephone = :telephone WHERE id = :id");
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            $_SESSION['message'] = "Informations mises à jour avec succès.";
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
        }

        header("Location: profile.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

$conn = null;
?>