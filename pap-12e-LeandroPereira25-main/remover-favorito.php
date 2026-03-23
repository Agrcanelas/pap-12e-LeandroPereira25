<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: meus-favoritos.php");
    exit();
}

$id_animal = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "DELETE FROM favoritos WHERE id_utilizador = ? AND id_animal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $id_animal);

if ($stmt->execute()) {
    header("Location: meus-favoritos.php?msg=removido");
} else {
    header("Location: meus-favoritos.php?msg=erro");
}

$stmt->close();
$conn->close();
?>
