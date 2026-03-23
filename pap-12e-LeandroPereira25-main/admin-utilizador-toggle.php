<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php?erro=sem_permissao');
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['acao'])) {
    header('Location: admin-dashboard.php?erro=parametros');
    exit();
}

$id_alvo = intval($_GET['id']);
$acao = $_GET['acao'];
$id_admin = (int) $_SESSION['user_id'];

if ($id_alvo <= 0 || ($acao !== 'ativar' && $acao !== 'inativar')) {
    header('Location: admin-dashboard.php?erro=parametros');
    exit();
}

if ($id_alvo === $id_admin && $acao === 'inativar') {
    header('Location: admin-dashboard.php?erro=auto_inativar');
    exit();
}

$novo_estado = $acao === 'ativar' ? 1 : 0;

$sql = "UPDATE utilizador SET ativo = ? WHERE id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $novo_estado, $id_alvo);

if ($stmt->execute() && $stmt->affected_rows >= 0) {
    header('Location: admin-dashboard.php?sucesso=estado_utilizador');
} else {
    header('Location: admin-dashboard.php?erro=estado_utilizador');
}

$stmt->close();
$conn->close();
