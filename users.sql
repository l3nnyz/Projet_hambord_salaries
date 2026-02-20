
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    motdepasse VARCHAR(255) NOT NULL,
    droit TINYINT NOT NULL DEFAULT 1 
);

INSERT INTO users (nom, motdepasse, droit) VALUES
('admin', 'adminpass', 2), -- Utilisateur administrateur
('user', 'userpass', 1);   -- Utilisateur standard