DROP TABLE IF EXISTS transactions_mobile;

CREATE TABLE transactions_mobile (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reference VARCHAR(20) NOT NULL UNIQUE,
    fedapay_id VARCHAR(100),
    telephone VARCHAR(15) NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    operateur ENUM('orange', 'mtn', 'moov') NOT NULL,
    item_type VARCHAR(50),
    item_id INT,
    statut ENUM('en_attente', 'valide', 'echoue', 'annule') NOT NULL DEFAULT 'en_attente',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    response_data JSON,
    INDEX idx_reference (reference),
    INDEX idx_fedapay_id (fedapay_id),
    INDEX idx_telephone (telephone),
    INDEX idx_statut (statut),
    INDEX idx_item (item_type, item_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
