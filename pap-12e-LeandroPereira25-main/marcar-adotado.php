<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: animais.php");
    exit();
}

$id_animal = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "UPDATE animal SET adotado = 1 WHERE id_animal = ? AND id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_animal, $user_id);

if($stmt->execute()) {
    header("Location: meus-animais.php?sucesso=adotado");
} else {
    header("Location: meus-animais.php?erro=1");
}
?>