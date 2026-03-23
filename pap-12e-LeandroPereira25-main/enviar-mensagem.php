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

// Processar anexo se enviado
$anexo_caminho = null;
if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
    $arquivo = $_FILES['anexo'];
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($extensao, $extensoes_permitidas) && $arquivo['size'] <= 5000000) {
        if (!file_exists('uploads/mensagens')) {
            mkdir('uploads/mensagens', 0777, true);
        }
        $nome_novo = 'mensagem_' . time() . '_' . rand(1000,9999) . '.' . $extensao;
        $caminho = 'uploads/mensagens/' . $nome_novo;
        if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
            $anexo_caminho = $caminho;
        }
    }
}

// Permitir envio com texto, com imagem, ou ambos.
if (empty($msg_texto) && empty($anexo_caminho)) {
    header('Location: mensagens.php?com=' . $id_destinatario . '&erro=vazio');
    exit();
}

// Verificar se coluna anexo existe (compatibilidade com base antiga)
$coluna_anexo_ok = false;
$check_anexo = $conn->query("SHOW COLUMNS FROM mensagem LIKE 'anexo'");
if ($check_anexo && $check_anexo->num_rows > 0) {
    $coluna_anexo_ok = true;
} else {
    // Tentar atualizar automaticamente a tabela para suportar anexos.
    if ($conn->query("ALTER TABLE mensagem ADD COLUMN anexo VARCHAR(255) NULL AFTER mensagem")) {
        $coluna_anexo_ok = true;
    }
}

if ($coluna_anexo_ok) {
    $sql = "INSERT INTO mensagem (id_remetente, id_destinatario, mensagem, anexo, data_envio) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $id_remetente, $id_destinatario, $msg_texto, $anexo_caminho);
} else {
    $sql = "INSERT INTO mensagem (id_remetente, id_destinatario, mensagem, data_envio) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $id_remetente, $id_destinatario, $msg_texto);
}

if ($stmt->execute()) {
    header('Location: mensagens.php?com=' . $id_destinatario . '&sucesso=1');
} else {
    header('Location: mensagens.php?com=' . $id_destinatario . '&erro=banco');
}

$stmt->close();
$conn->close();
?>
