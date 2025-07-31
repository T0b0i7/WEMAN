<?php
require_once '../config/connexion.php';

// Définir l'en-tête de réponse comme JSON
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $question1 = $_POST['question1'];
    $question2 = $_POST['question2'];
    $question3 = $_POST['question3'];
    $question4 = $_POST['question4'];
    $question5 = $_POST['question5'];

    error_log("email: $email");
    error_log("question1: $question1");
    error_log("question2: $question2");
    error_log("question3: $question3");
    error_log("question4: $question4");
    error_log("question5: $question5");

    if (empty($email) || empty($question1) || empty($question2) || empty($question3) || empty($question4) || empty($question5)) {
        $response['message'] = "Tous les champs sont obligatoires.";
    } else {
        try {
            // Vérifiez que l'utilisateur existe
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                $utilisateur_id = $user['id'];
                $stmt = $pdo->prepare("SELECT * FROM reponses_questionnaire WHERE utilisateur_id = :utilisateur_id");
                $stmt->bindParam(':utilisateur_id', $utilisateur_id);
                $stmt->execute();
                $reponses = $stmt->fetch();

                if ($reponses) {
                    if ($reponses['question1'] == $question1 &&
                        $reponses['question2'] == $question2 &&
                        $reponses['question3'] == $question3 &&
                        $reponses['question4'] == $question4 &&
                        $reponses['question5'] == $question5) {
                        
                        // Générer un nouveau mot de passe sécurisé
                        $nouveau_mot_de_passe = bin2hex(random_bytes(4)); // 8 caractères hexadécimaux
                        $mot_de_passe_hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);

                        // Mettre à jour le mot de passe dans la base de données
                        $update_stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe_hash = :mot_de_passe_hash WHERE id = :utilisateur_id");
                        $update_stmt->bindParam(':mot_de_passe_hash', $mot_de_passe_hash);
                        $update_stmt->bindParam(':utilisateur_id', $utilisateur_id);
                        $update_stmt->execute();

                        $response['success'] = true;
                        $response['nouveau_mot_de_passe'] = $nouveau_mot_de_passe;
                    } else {
                        $response['message'] = "Les réponses aux questions de sécurité sont incorrectes.";
                    }
                } else {
                    $response['message'] = "Réponses aux questions de sécurité non trouvées.";
                }
            } else {
                $response['message'] = "Utilisateur non trouvé.";
            }
        } catch (PDOException $e) {
            $response['message'] = "Erreur de base de données : " . $e->getMessage();
        } catch (Exception $e) {
            $response['message'] = "Erreur : " . $e->getMessage();
        }
    }
} else {
    $response['message'] = "Méthode de requête invalide.";
}

echo json_encode($response);
?>
