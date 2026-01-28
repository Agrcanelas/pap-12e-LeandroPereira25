<?php 
require_once 'ligaDB.php';

// Verificar se utilizador está logado
if (!isset($_SESSION['logado']) || !$_SESSION['logado']) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mensagens.php');
    exit();
}

$id_remetente = $_SESSION['user_id'];
$id_destinatario = intval($_POST['para_id']);
$msg_texto = trim($_POST['conteudo']);

// Validações básicas
if (empty($msg_texto)) {
    header('Location: mensagens.php?com=' . $id_destinatario . '&erro=vazio');
    exit();
}

if (strlen($msg_texto) > 1000) {
    header('Location: mensagens.php?com=' . $id_destinatario . '&erro=longo');
    exit();
}

// Verificar se o destinatário existe
$sql_check = "SELECT id_utilizador FROM utilizador WHERE id_utilizador = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id_destinatario);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows === 0) {
    header('Location: mensagens.php?erro=utilizador');
    exit();
}

// Inserir mensagem
$sql = "INSERT INTO mensagem (id_remetente, id_destinatario, mensagem, data_envio) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $id_remetente, $id_destinatario, $msg_texto);

if ($stmt->execute()) {
    header('Location: mensagens.php?com=' . $id_destinatario . '&sucesso=1');
} else {
    header('Location: mensagens.php?com=' . $id_destinatario . '&erro=banco');
}

$stmt->close();
$conn->close();
?>
