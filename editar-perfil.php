<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $telefone = trim($_POST['telefone']);
    $biografia = trim($_POST['biografia']);
    
    $sql = "UPDATE utilizador SET nome = ?, telefone = ?, biografia = ? WHERE id_utilizador = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $telefone, $biografia, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['user_nome'] = $nome;
        header("Location: dashboard.php?sucesso=atualizado");
        exit();
    }
}

// Buscar dados atuais
$sql = "SELECT nome, telefone, biografia FROM utilizador WHERE id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
$user = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="estilo-login.css">
</head>
<body>
    <div class="container-login">
        <div class="caixa-login">
            <h2>Editar Perfil</h2>
            <form method="POST">
                <div class="grupo-input">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                </div>
                <div class="grupo-input">
                    <label>Telefone</label>
                    <input type="tel" name="telefone" value="<?php echo htmlspecialchars($user['telefone']); ?>">
                </div>
                <div class="grupo-input">
                    <label>Biografia</label>
                    <textarea name="biografia" rows="5" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($user['biografia']); ?></textarea>
                </div>
                <button type="submit" class="botao-login">Guardar Alterações</button>
            </form>
            <a href="dashboard.php" class="link-voltar">← Voltar ao Perfil</a>
        </div>
    </div>
</body>
</html>