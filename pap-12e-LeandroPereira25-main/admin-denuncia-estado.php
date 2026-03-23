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
$estado = trim((string) ($_GET['estado'] ?? ''));
$permitidos = ['pendente', 'em_analise', 'resolvida', 'rejeitada'];

if ($id_denuncia <= 0 || !in_array($estado, $permitidos, true)) {
    header('Location: admin-denuncias.php?erro=1');
    exit();
}

$sql = "UPDATE denuncias SET estado = ? WHERE id_denuncia = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $estado, $id_denuncia);

if ($stmt->execute()) {
    header('Location: admin-denuncias.php?sucesso=1');
} else {
    header('Location: admin-denuncias.php?erro=1');
}

$stmt->close();
$conn->close();
