<?php
require_once 'ligaDB.php';

echo "<h2>Inicializando novas funcionalidades...</h2>";

// 1. Tabela de Favoritos
$sql_favoritos = "CREATE TABLE IF NOT EXISTS favoritos (
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

if ($conn->query($sql_favoritos) === TRUE) {
    echo "✅ Tabela de favoritos criada/verificada!<br>";
} else {
    echo "❌ Erro ao criar favoritos: " . $conn->error . "<br>";
}

// 2. Tabela de Avaliações
$sql_avaliacoes = "CREATE TABLE IF NOT EXISTS avaliacoes (
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

if ($conn->query($sql_avaliacoes) === TRUE) {
    echo "✅ Tabela de avaliações criada/verificada!<br>";
} else {
    echo "❌ Erro ao criar avaliações: " . $conn->error . "<br>";
}

echo "<p><strong>Setup completo!</strong> Todas as tabelas estão prontas.</p>";

$conn->close();
?>
