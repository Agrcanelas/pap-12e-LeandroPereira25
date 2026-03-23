<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: meus-animais.php");
    exit();
}

$id_animal = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

$sql_owner = "SELECT id_utilizador FROM animal WHERE id_animal = ?";
$stmt_owner = $conn->prepare($sql_owner);
$stmt_owner->bind_param("i", $id_animal);
$stmt_owner->execute();
$resultado_owner = $stmt_owner->get_result();

if (!$resultado_owner || $resultado_owner->num_rows === 0) {
    $stmt_owner->close();
    header("Location: animais.php?erro=animal_nao_encontrado");
    exit();
}

$animal = $resultado_owner->fetch_assoc();
$stmt_owner->close();

if ((int)$animal['id_utilizador'] !== (int)$user_id && !$is_admin) {
    header("Location: animais.php?erro=sem_permissao");
    exit();
}

$sql = "DELETE FROM animal WHERE id_animal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_animal);

if ($stmt->execute()) {
    $destino = $is_admin ? "animais.php?sucesso=removido" : "meus-animais.php?sucesso=removido";
    header("Location: " . $destino);
} else {
    $destino = $is_admin ? "animais.php?erro=remover" : "meus-animais.php?erro=remover";
    header("Location: " . $destino);
}

$stmt->close();
$conn->close();
?>