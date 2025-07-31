<?php
// filepath: c:\xampp\htdocs\WEMANTCHE\admin\users\get_users.php
require_once '../../config/connexion.php';

header('Content-Type: application/json');

try {
    // Vérifiez la connexion à la base de données
    if ($pdo) {
        // Requête pour récupérer les utilisateurs
        $sql = "SELECT id, prenom, nom, email, telephone, mot_de_passe_hash, role, statut, 
                DATE_FORMAT(cree_a, '%Y-%m-%d %H:%i:%s') as cree_a, 
                DATE_FORMAT(mis_a_jour_a, '%Y-%m-%d %H:%i:%s') as mis_a_jour_a 
                FROM utilisateurs 
                WHERE statut != 'supprime' 
                ORDER BY id DESC";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {
            // Formatage des données pour correspondre à l'attente du frontend
            $formattedUsers = array_map(function($user) {
                return [
                    'id' => (int)$user['id'],
                    'prenom' => htmlspecialchars($user['prenom']),
                    'nom' => htmlspecialchars($user['nom']),
                    'email' => htmlspecialchars($user['email']),
                    'telephone' => htmlspecialchars($user['telephone']),
                    'role' => $user['role'],
                    'statut' => $user['statut'],
                    'cree_a' => $user['cree_a'],
                    'mis_a_jour_a' => $user['mis_a_jour_a']
                ];
            }, $users);

            echo json_encode([
                'success' => true,
                'users' => $formattedUsers,
                'total' => count($formattedUsers),
                'actifs' => count(array_filter($formattedUsers, function($user) { 
                    return $user['statut'] === 'actif'; 
                })),
                'en_attente' => count(array_filter($formattedUsers, function($user) { 
                    return $user['statut'] === 'en_attente'; 
                }))
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'users' => [],
                'total' => 0,
                'actifs' => 0,
                'en_attente' => 0
            ]);
        }
    } else {
        throw new PDOException('La connexion à la base de données a échoué');
    }
} catch (PDOException $e) {
    error_log("Erreur PDO : " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des utilisateurs: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Erreur générale : " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur inattendue est survenue'
    ]);
}
?>