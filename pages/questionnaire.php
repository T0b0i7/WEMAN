<?php
require_once '../config/connexion.php';

// Définir l'en-tête de réponse comme JSON
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $utilisateur_id = $_POST['utilisateur_id']; // Assurez-vous que cet ID est passé correctement
    $question1 = $_POST['question1'];
    $question2 = $_POST['question2'];
    $question3 = $_POST['question3'];
    $question4 = $_POST['question4'];
    $question5 = $_POST['question5'];

    if (empty($utilisateur_id) || empty($question1) || empty($question2) || empty($question3) || empty($question4) || empty($question5)) {
        $response['message'] = "Tous les champs sont obligatoires.";
    } else {
        // Vérifiez que l'utilisateur existe
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $utilisateur_id);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            try {
                $stmt = $pdo->prepare("INSERT INTO reponses_questionnaire (utilisateur_id, question1, question2, question3, question4, question5) VALUES (:utilisateur_id, :question1, :question2, :question3, :question4, :question5)");
                $stmt->bindParam(':utilisateur_id', $utilisateur_id);
                $stmt->bindParam(':question1', $question1);
                $stmt->bindParam(':question2', $question2);
                $stmt->bindParam(':question3', $question3);
                $stmt->bindParam(':question4', $question4);
                $stmt->bindParam(':question5', $question5);
                $stmt->execute();

                $response['success'] = true;
                $response['message'] = "Réponses enregistrées avec succès";
            } catch (PDOException $e) {
                $response['message'] = "Erreur : " . $e->getMessage();
            }
        } else {
            $response['message'] = "Utilisateur non trouvé.";
        }
    }
} else {
    $response['message'] = "Méthode de requête invalide.";
}

echo json_encode($response);
?>