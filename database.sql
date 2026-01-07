-- Criar Base de Dados
CREATE DATABASE IF NOT EXISTS sas_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar a Base de Dados
USE sas_database;

-- Criar Tabela de Utilizadores
CREATE TABLE IF NOT EXISTS utilizadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir utilizador de teste (password: teste123)
INSERT INTO utilizadores (nome, email, telefone, password) VALUES 
('Utilizador Teste', 'teste@sas.pt', '+351 912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Ver todos os utilizadores
-- SELECT * FROM utilizadores;