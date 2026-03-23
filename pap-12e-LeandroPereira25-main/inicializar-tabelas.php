<?php
// Script para garantir que as tabelas necessárias existem

require_once 'ligaDB.php';

echo "=== Inicializando Tabelas Necessárias ===<br><br>";

// 1. Verificar e criar tabela favoritos
echo "<h3>Tabela Favoritos</h3>";

$sql_check = "SHOW TABLES LIKE 'favoritos'";
$result = $conn->query($sql_check);

if ($result && $result->num_rows > 0) {
    echo "✓ Tabela favoritos já existe<br>";
} else {
    echo "↻ Criando tabela favoritos...<br>";
    
    $sql = "CREATE TABLE IF NOT EXISTS favoritos (
        id_favorito INT AUTO_INCREMENT PRIMARY KEY,
        id_utilizador INT NOT NULL,
        id_animal INT NOT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_favorito (id_utilizador, id_animal),
        FOREIGN KEY (id_utilizador) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
        FOREIGN KEY (id_animal) REFERENCES animal(id_animal) ON DELETE CASCADE,
        INDEX idx_utilizador (id_utilizador),
        INDEX idx_animal (id_animal)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela favoritos criada com sucesso<br>";
    } else {
        echo "✗ Erro ao criar tabela: " . $conn->error . "<br>";
    }
}

echo "<h3>Tabela Denuncias</h3>";

$sql_check = "SHOW TABLES LIKE 'denuncias'";
$result = $conn->query($sql_check);

if ($result && $result->num_rows > 0) {
    echo "✓ Tabela denuncias já existe<br>";
} else {
    echo "↻ Criando tabela denuncias...<br>";

    $sql = "CREATE TABLE IF NOT EXISTS denuncias (
        id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
        id_denunciante INT NOT NULL,
        id_denunciado INT NOT NULL,
        assunto VARCHAR(150) NOT NULL,
        descricao TEXT NOT NULL,
        motivo VARCHAR(250) DEFAULT NULL,
        estado ENUM('pendente','em_analise','resolvida','rejeitada') NOT NULL DEFAULT 'pendente',
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (id_denunciante) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
        FOREIGN KEY (id_denunciado) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
        INDEX idx_denunciante (id_denunciante),
        INDEX idx_denunciado (id_denunciado),
        INDEX idx_estado (estado)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela denuncias criada com sucesso<br>";
    } else {
        echo "✗ Erro ao criar tabela: " . $conn->error . "<br>";
    }
}

echo "<br><hr>";
echo "<h2>✅ Inicialização Completa!</h2>";
echo "<p><a href='animais.php'>Voltar para Animais</a></p>";

$conn->close();
?>
