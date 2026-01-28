<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: animais.php");
    exit();
}

$id_animal = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Buscar foto para remover
$sql = "SELECT foto_animal FROM animal WHERE id_animal = ? AND id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_animal, $user_id);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows > 0) {
    $animal = $resultado->fetch_assoc();
    
    // Remover foto se existir
    if($animal['foto_animal'] && file_exists($animal['foto_animal']) && $animal['foto_animal'] != 'uploads/animal-default.jpg') {
        unlink($animal['foto_animal']);
    }
    
    // Remover do banco
    $sql_delete = "DELETE FROM animal WHERE id_animal = ? AND id_utilizador = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $id_animal, $user_id);
    
    if($stmt_delete->execute()) {
        header("Location: meus-animais.php?sucesso=removido");
    } else {
        header("Location: meus-animais.php?erro=1");
    }
} else {
    header("Location: meus-animais.php?erro=1");
}
?>