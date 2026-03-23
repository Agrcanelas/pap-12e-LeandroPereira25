<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: meus-animais.php");
    exit();
}

$id_animal = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "DELETE FROM animal WHERE id_animal = ? AND id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_animal, $user_id);

if ($stmt->execute()) {
    header("Location: meus-animais.php?sucesso=removido");
} else {
    header("Location: meus-animais.php?erro=remover");
}

$stmt->close();
$conn->close();
?>