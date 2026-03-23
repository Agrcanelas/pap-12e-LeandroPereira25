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

$id_denuncia = (int) ($_GET['id'] ?? 0);
if ($id_denuncia <= 0) {
    header('Location: admin-denuncias.php?erro=nao_encontrada');
    exit();
}

$sql_check = "SELECT estado FROM denuncias WHERE id_denuncia = ? LIMIT 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id_denuncia);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if (!$result_check || $result_check->num_rows === 0) {
    $stmt_check->close();
    header('Location: admin-denuncias.php?erro=nao_encontrada');
    exit();
}

$denuncia = $result_check->fetch_assoc();
$stmt_check->close();

if (($denuncia['estado'] ?? '') !== 'resolvida') {
    header('Location: admin-denuncias.php?erro=apagar_nao_permitido');
    exit();
}

$sql_delete = "DELETE FROM denuncias WHERE id_denuncia = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $id_denuncia);

if ($stmt_delete->execute()) {
    header('Location: admin-denuncias.php?sucesso=apagada');
} else {
    header('Location: admin-denuncias.php?erro=1');
}

$stmt_delete->close();
$conn->close();
