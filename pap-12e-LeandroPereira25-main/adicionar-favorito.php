<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: animais.php");
    exit();
}

$id_animal = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$sql = "INSERT INTO favoritos (id_utilizador, id_animal) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $id_animal);

if ($stmt->execute()) {
    header("Location: animais.php?msg=favorito_adicionado");
} else {
    header("Location: animais.php?msg=favorito_erro");
}

$stmt->close();
$conn->close();
?>
