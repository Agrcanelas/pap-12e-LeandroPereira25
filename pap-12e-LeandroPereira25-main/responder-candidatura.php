<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: candidaturas.php");
    exit();
}

$id_candidatura = intval($_GET['id']);
$novo_status = $_GET['status']; // aceite ou rejeitada

// Validar status
if (!in_array($novo_status, ['aceite', 'rejeitada'])) {
    header("Location: candidaturas.php?erro=status_invalido");
    exit();
}

// Buscar candidatura e verificar que pertence ao utilizador logado
$sql_check = "SELECT c.*, a.id_utilizador FROM candidatura_adocao c
              JOIN animal a ON c.id_animal = a.id_animal
              WHERE c.id_candidatura = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id_candidatura);
$stmt_check->execute();
$candidatura = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();

if (!$candidatura || $candidatura['id_utilizador'] != $_SESSION['user_id']) {
    header("Location: candidaturas.php?erro=nao_autorizado");
    exit();
}

// Atualizar status
$sql = "UPDATE candidatura_adocao SET status = ? WHERE id_candidatura = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $novo_status, $id_candidatura);

if ($stmt->execute()) {
    // Se aceite, marcar animal como adotado
    if ($novo_status === 'aceite') {
        $sql_adotar = "UPDATE animal SET adotado = 1 WHERE id_animal = ?";
        $stmt_adotar = $conn->prepare($sql_adotar);
        $stmt_adotar->bind_param("i", $candidatura['id_animal']);
        $stmt_adotar->execute();
        $stmt_adotar->close();
    }

    header("Location: candidaturas.php?sucesso=" . $novo_status);
} else {
    header("Location: candidaturas.php?erro=banco_dados");
}

$stmt->close();
$conn->close();
?>
