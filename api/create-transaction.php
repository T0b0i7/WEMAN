<?php
require_once '../config/fedapay.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $transaction = FedaPayConfig::createTransaction(
        $input['amount'],
        $input['description'],
        [
            'firstname' => $input['firstname'],
            'lastname' => $input['lastname'],
            'email' => $input['email'],
            'phone' => $input['phone']
        ]
    );

    if ($transaction) {
        echo json_encode(['token' => $transaction->token]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la création de la transaction']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
