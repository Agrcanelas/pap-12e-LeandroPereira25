<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: animais.php');
    exit();
}

$id_denunciante = (int) ($_SESSION['user_id'] ?? 0);
$id_denunciado = (int) ($_POST['id_denunciado'] ?? 0);
$assunto = trim((string) ($_POST['assunto'] ?? ''));
$descricao = trim((string) ($_POST['descricao'] ?? ''));

if ($id_denunciante <= 0 || $id_denunciado <= 0 || $id_denunciante === $id_denunciado) {
    header('Location: animais.php?erro=denuncia_invalida');
    exit();
}

if ($assunto === '') {
    header('Location: denunciar-perfil.php?id=' . $id_denunciado . '&erro=assunto_obrigatorio');
    exit();
}

if ($descricao === '') {
    header('Location: denunciar-perfil.php?id=' . $id_denunciado . '&erro=descricao_obrigatoria&assunto=' . urlencode($assunto));
    exit();
}

if (mb_strlen($descricao) < 10) {
    header('Location: denunciar-perfil.php?id=' . $id_denunciado . '&erro=descricao_curta&assunto=' . urlencode($assunto) . '&descricao=' . urlencode($descricao));
    exit();
}

$assunto = mb_substr($assunto, 0, 150);
$descricao = mb_substr($descricao, 0, 2000);

$sql_tabela = "CREATE TABLE IF NOT EXISTS denuncias (
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
$conn->query($sql_tabela);

$conn->query("ALTER TABLE denuncias ADD COLUMN IF NOT EXISTS assunto VARCHAR(150) NOT NULL DEFAULT '' AFTER id_denunciado");
$conn->query("ALTER TABLE denuncias ADD COLUMN IF NOT EXISTS descricao TEXT NULL AFTER assunto");

$sql_check = "SELECT id_denuncia FROM denuncias WHERE id_denunciante = ? AND id_denunciado = ? AND estado IN ('pendente', 'em_analise') LIMIT 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_denunciante, $id_denunciado);
$stmt_check->execute();
$ja_existe = $stmt_check->get_result()->num_rows > 0;
$stmt_check->close();

if ($ja_existe) {
    header('Location: denunciar-perfil.php?id=' . $id_denunciado . '&erro=denuncia_existente&assunto=' . urlencode($assunto) . '&descricao=' . urlencode($descricao));
    exit();
}

$sql = "INSERT INTO denuncias (id_denunciante, id_denunciado, assunto, descricao, motivo) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$motivo_legacy = mb_substr($assunto . ' - ' . $descricao, 0, 250);
$stmt->bind_param("iisss", $id_denunciante, $id_denunciado, $assunto, $descricao, $motivo_legacy);

if ($stmt->execute()) {
    header('Location: perfil-utilizador.php?id=' . $id_denunciado . '&sucesso=denuncia_enviada');
} else {
    header('Location: perfil-utilizador.php?id=' . $id_denunciado . '&erro=denuncia_erro');
}

$stmt->close();
$conn->close();
