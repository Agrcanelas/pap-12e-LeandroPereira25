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

// 2. Tabela de Candidaturas para Adoção
$sql_candidaturas = "CREATE TABLE IF NOT EXISTS candidatura_adocao (
    id_candidatura INT AUTO_INCREMENT PRIMARY KEY,
    id_utilizador INT NOT NULL,
    id_animal INT NOT NULL,
    situacao_habitacional VARCHAR(100),
    experiencia_anterior TEXT,
    motivo_adocao TEXT,
    dados_contacto VARCHAR(255),
    status ENUM('pendente', 'aceite', 'rejeitada') DEFAULT 'pendente',
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilizador) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
    FOREIGN KEY (id_animal) REFERENCES animal(id_animal) ON DELETE CASCADE,
    INDEX idx_animal (id_animal),
    INDEX idx_utilizador (id_utilizador),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_candidaturas) === TRUE) {
    echo "✅ Tabela de candidaturas criada/verificada!<br>";
} else {
    echo "❌ Erro ao criar candidaturas: " . $conn->error . "<br>";
}

// 3. Tabela de Avaliações
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

// 4. Adicionar coluna de estatísticas na tabela animal (se não existir)
$check_stats = $conn->query("SHOW COLUMNS FROM animal LIKE 'total_candidaturas'");
if (!$check_stats || $check_stats->num_rows === 0) {
    $sql_alter = "ALTER TABLE animal ADD COLUMN total_candidaturas INT DEFAULT 0";
    if ($conn->query($sql_alter) === TRUE) {
        echo "✅ Coluna de estatísticas adicionada!<br>";
    } else {
        echo "⚠️ Coluna já existe ou erro: " . $conn->error . "<br>";
    }
}

echo "<p><strong>Setup completo!</strong> Todas as tabelas estão prontas.</p>";

$conn->close();
?>
