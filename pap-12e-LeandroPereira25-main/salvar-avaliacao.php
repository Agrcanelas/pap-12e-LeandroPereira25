<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: animais.php");
    exit();
}

$id_animal = intval($_POST['id_animal']);
$user_id = $_SESSION['user_id'];
$classificacao = intval($_POST['classificacao']);
$comentario = trim($_POST['comentario'] ?? '');

// Validar classificação
if ($classificacao < 1 || $classificacao > 5) {
    header("Location: detalhes-animal.php?id=$id_animal&erro=classificacao_invalida");
    exit();
}

// Verificar se substituir avaliação anterior ou criar nova
$check_sql = "SELECT id_avaliacao FROM avaliacoes WHERE id_utilizador = ? AND id_animal = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $id_animal);
$check_stmt->execute();
$existe = $check_stmt->get_result()->num_rows > 0;
$check_stmt->close();

if ($existe) {
    // Atualizar avaliação existente
    $sql = "UPDATE avaliacoes SET classificacao = ?, comentario = ? WHERE id_utilizador = ? AND id_animal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $classificacao, $comentario, $user_id, $id_animal);
} else {
    // Criar nova avaliação
    $sql = "INSERT INTO avaliacoes (id_utilizador, id_animal, classificacao, comentario) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $id_animal, $classificacao, $comentario);
}

if ($stmt->execute()) {
    header("Location: detalhes-animal.php?id=$id_animal&sucesso=avaliacao_salva");
} else {
    header("Location: detalhes-animal.php?id=$id_animal&erro=avaliacao_erro");
}

$stmt->close();
$conn->close();
?>
