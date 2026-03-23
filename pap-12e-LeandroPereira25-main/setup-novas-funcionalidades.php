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

// 2. Tabela de Denuncias
$sql_denuncias = "CREATE TABLE IF NOT EXISTS denuncias (
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

if ($conn->query($sql_denuncias) === TRUE) {
    echo "✅ Tabela de denuncias criada/verificada!<br>";
} else {
    echo "❌ Erro ao criar denuncias: " . $conn->error . "<br>";
}

echo "<p><strong>Setup completo!</strong> Todas as tabelas estão prontas.</p>";

$conn->close();
?>
