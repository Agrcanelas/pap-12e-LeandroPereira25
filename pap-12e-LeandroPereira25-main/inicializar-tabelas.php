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

// 2. Verificar e criar tabela avaliacoes
echo "<h3>Tabela Avaliações</h3>";

$sql_check = "SHOW TABLES LIKE 'avaliacoes'";
$result = $conn->query($sql_check);

if ($result && $result->num_rows > 0) {
    echo "✓ Tabela avaliacoes já existe<br>";
} else {
    echo "↻ Criando tabela avaliacoes...<br>";
    
    $sql = "CREATE TABLE IF NOT EXISTS avaliacoes (
        id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
        id_utilizador INT NOT NULL,
        id_animal INT NOT NULL,
        classificacao INT NOT NULL CHECK (classificacao >= 1 AND classificacao <= 5),
        comentario TEXT,
        data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_utilizador) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
        FOREIGN KEY (id_animal) REFERENCES animal(id_animal) ON DELETE CASCADE,
        INDEX idx_animal (id_animal),
        INDEX idx_utilizador (id_utilizador)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "✓ Tabela avaliacoes criada com sucesso<br>";
    } else {
        echo "✗ Erro ao criar tabela: " . $conn->error . "<br>";
    }
}

echo "<br><hr>";
echo "<h2>✅ Inicialização Completa!</h2>";
echo "<p><a href='animais.php'>Voltar para Animais</a></p>";

$conn->close();
?>
