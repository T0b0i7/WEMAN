<?php
require_once '../../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $statut = $_POST['status'];
    $description = $_POST['description'];

    // Récupérer l'email de l'utilisateur
    $sql = "SELECT u.email, dr.sujet FROM demandes_redaction dr 
            LEFT JOIN utilisateurs u ON dr.utilisateur_id = u.id 
            WHERE dr.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $demande = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($demande) {
        $email = $demande['email'];
        $sujet = $demande['sujet'];

        // Mettre à jour le statut
        $sql = "UPDATE demandes_redaction 
                SET statut = :statut, description = :description, date_modification = NOW() 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['statut' => $statut, 'description' => $description, 'id' => $id])) {
            // Envoyer un email à l'utilisateur
            $to = $email;
            $subject = "Mise à jour de votre demande : $sujet";
            $message = "Bonjour,\n\nVotre demande a été mise à jour avec le statut suivant : ".ucfirst(str_replace('_', ' ', $statut)).".\n\nCordialement,\nL'équipe WEMANTCHE.";
            $headers = "From: admin@weman.com";

            mail($to, $subject, $message, $headers);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Erreur lors de la mise à jour']);
        }
    } else {
        echo json_encode(['error' => 'Demande introuvable']);
    }
}
?>

<!-- filepath: c:\xampp\htdocs\WEMAN\admin\redaction\demandes.php -->
<thead>
    <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Sujet</th>
        <th>Description</th> <!-- Nouvelle colonne -->
        <th>Type</th>
        <th>Date</th>
        <th>Deadline</th>
        <th>Statut</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php
    if ($result) {
        foreach($result as $row) {
            $statut_class = [
                'en_attente' => 'warning',
                'en_cours' => 'info',
                'termine' => 'success',
                'annule' => 'danger',
                'rejetee' => 'danger',
                'validee' => 'success'
            ];
            echo "<tr>
                <td>".$row['id']."</td>
                <td>".$row['prenom']." ".$row['nom']."</td>
                <td>".$row['sujet']."</td>
                <td>".substr($row['description'], 0, 50)."...</td> <!-- Affichage de la description -->
                <td>".ucfirst($row['type_document'])."</td>
                <td>".date('d/m/Y', strtotime($row['date_creation']))."</td>
                <td>".date('d/m/Y', strtotime($row['delai_souhaite']))."</td>
                <td><span class='badge bg-".$statut_class[$row['statut']]."'>".
                    str_replace('_', ' ', ucfirst($row['statut']))."</span></td>
                <td>
                    <button class='btn btn-sm btn-info view-btn' data-id='".$row['id']."'>
                        <i class='fas fa-eye'></i>
                    </button>
                    <button class='btn btn-sm btn-success edit-btn' data-id='".$row['id']."'>
                        <i class='fas fa-edit'></i>
                    </button>
                    <button class='btn btn-sm btn-danger delete-btn' data-id='".$row['id']."'>
                        <i class='fas fa-trash'></i>
                    </button>
                </td>
            </tr>";
        }
    }
    ?>
</tbody>