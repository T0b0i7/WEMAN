<?php
require_once '../config/connexion.php';
require_once '../vendor/autoload.php';

\FedaPay\FedaPay::setApiKey('sk_live_MrKyCgZsuk-YVTuw4EMOTo8g');
\FedaPay\FedaPay::setEnvironment('sandbox');

// Récupérer les données brutes du webhook
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log pour debug
file_put_contents('webhook.log', date('Y-m-d H:i:s') . ' - ' . $input . PHP_EOL, FILE_APPEND);

if ($data && isset($data['event'])) {
    try {
        // Vérifier le type d'événement
        switch ($data['event']) {
            case 'transaction.success':
                $fedapay_id = $data['transaction']['id'];
                // Mettre à jour la transaction
                $stmt = $pdo->prepare("UPDATE transactions_mobile 
                                     SET statut = 'valide', 
                                         response_data = :response_data,
                                         date_modification = NOW()
                                     WHERE fedapay_id = :fedapay_id");
                $stmt->execute([
                    ':fedapay_id' => $fedapay_id,
                    ':response_data' => json_encode($data)
                ]);
                break;

            case 'transaction.failed':
                $fedapay_id = $data['transaction']['id'];
                // Mettre à jour la transaction en échec
                $stmt = $pdo->prepare("UPDATE transactions_mobile 
                                     SET statut = 'echoue',
                                         response_data = :response_data,
                                         date_modification = NOW()
                                     WHERE fedapay_id = :fedapay_id");
                $stmt->execute([
                    ':fedapay_id' => $fedapay_id,
                    ':response_data' => json_encode($data)
                ]);
                break;
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid webhook data']);
}
