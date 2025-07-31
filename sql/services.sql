CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-star',
    actif BOOLEAN DEFAULT true,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_actif (actif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données d'exemple
INSERT INTO services (titre, description, prix, icon) VALUES
('Consultation Juridique', 'Consultation juridique personnalisée avec un expert', 25000, 'fa-scale-balanced'),
('Formation Professionnelle', 'Formation complète en droit des affaires', 150000, 'fa-graduation-cap'),
('Assistance Administrative', 'Aide pour vos démarches administratives', 35000, 'fa-file-contract');
