<?php
require_once 'ligaDB.php';

// Criar tabela mensagem se não existir
$sql_create_mensagem = "CREATE TABLE IF NOT EXISTS mensagem (
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    id_remetente INT NOT NULL,
    id_destinatario INT NOT NULL,
    conteudo TEXT NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_remetente) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
    FOREIGN KEY (id_destinatario) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
    INDEX idx_remetente (id_remetente),
    INDEX idx_destinatario (id_destinatario),
    INDEX idx_conversa (id_remetente, id_destinatario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_create_mensagem) === TRUE) {
    echo "✅ Tabela de mensagens criada/verificada com sucesso!";
} else {
    echo "❌ Erro ao criar tabela: " . $conn->error;
}

$conn->close();
?>
