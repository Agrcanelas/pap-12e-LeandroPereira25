<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: animais.php");
    exit();
}

$id_animal = intval($_POST['id_animal']);
$user_id = $_SESSION['user_id'];
$situacao_habitacional = trim($_POST['situacao_habitacional']);
$experiencia_anterior = trim($_POST['experiencia_anterior']);
$motivo_adocao = trim($_POST['motivo_adocao']);
$composicao_familia = trim($_POST['composicao_familia'] ?? '');
$dados_contacto = trim($_POST['telefone']);

// Validar
if (empty($situacao_habitacional) || empty($experiencia_anterior) || empty($motivo_adocao)) {
    header("Location: formulario-candidatura.php?id=$id_animal&erro=campos_obrigatorios");
    exit();
}

// Verificar se já existe candidatura ativa
$check_sql = "SELECT id_candidatura FROM candidatura_adocao 
              WHERE id_utilizador = ? AND id_animal = ? AND status = 'pendente'";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $id_animal);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows > 0) {
    header("Location: formulario-candidatura.php?id=$id_animal&erro=ja_candidatado");
    exit();
}
$check_stmt->close();

// Guardar candidatura
$sql = "INSERT INTO candidatura_adocao (id_utilizador, id_animal, situacao_habitacional, experiencia_anterior, motivo_adocao, dados_contacto, status) 
        VALUES (?, ?, ?, ?, ?, ?, 'pendente')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iissss", $user_id, $id_animal, $situacao_habitacional, $experiencia_anterior, $motivo_adocao, $dados_contacto);

if ($stmt->execute()) {
    header("Location: detalhes-animal.php?id=$id_animal&sucesso=candidatura_enviada");
} else {
    header("Location: formulario-candidatura.php?id=$id_animal&erro=banco_dados");
}

$stmt->close();
$conn->close();
?>
