<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit();
}

$id_denunciado = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$id_denunciante = (int) ($_SESSION['user_id'] ?? 0);

if ($id_denunciado <= 0 || $id_denunciante <= 0 || $id_denunciado === $id_denunciante) {
    header('Location: animais.php?erro=denuncia_invalida');
    exit();
}

$sql = "SELECT id_utilizador, nome FROM utilizador WHERE id_utilizador = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_denunciado);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    header('Location: animais.php?erro=perfil_nao_encontrado');
    exit();
}

$utilizador = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denunciar Perfil - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo-login.css">
</head>
<body>
    <div class="container-login">
        <div class="caixa-login" style="max-width: 700px;">
            <h2>Denunciar Perfil</h2>
            <p style="margin-bottom: 18px; color: #666;">Estás a denunciar o perfil de <strong><?php echo htmlspecialchars($utilizador['nome']); ?></strong>.</p>

            <?php if (isset($_GET['erro'])): ?>
                <div style="margin-bottom: 12px; padding: 10px 12px; border-radius: 8px; background: #fdecec; color: #b53a3a; border: 1px solid #f3c2c2;">
                    <?php
                        if ($_GET['erro'] === 'denuncia_existente') {
                            echo 'Já existe uma denúncia tua em análise para este perfil.';
                        } elseif ($_GET['erro'] === 'assunto_obrigatorio') {
                            echo 'Indica o assunto da denúncia.';
                        } elseif ($_GET['erro'] === 'descricao_obrigatoria') {
                            echo 'Escreve a descrição da denúncia.';
                        } elseif ($_GET['erro'] === 'descricao_curta') {
                            echo 'A descrição deve ter pelo menos 10 caracteres.';
                        } else {
                            echo 'Não foi possível enviar a denúncia.';
                        }
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="denunciar-utilizador.php" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="hidden" name="id_denunciado" value="<?php echo (int) $id_denunciado; ?>">

                <div class="grupo-input">
                    <label for="assunto">Assunto *</label>
                    <input id="assunto" type="text" name="assunto" maxlength="150" required placeholder="Ex: Comportamento abusivo" value="<?php echo htmlspecialchars((string) ($_GET['assunto'] ?? '')); ?>">
                </div>

                <div class="grupo-input">
                    <label for="descricao">Descrição *</label>
                    <textarea id="descricao" name="descricao" rows="6" required minlength="10" maxlength="2000" placeholder="Explica detalhadamente o motivo da denúncia..." style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; resize: vertical; font-family: inherit;"><?php echo htmlspecialchars((string) ($_GET['descricao'] ?? '')); ?></textarea>
                </div>

                <button type="submit" class="botao-login">Enviar Denúncia</button>
            </form>

            <a href="perfil-utilizador.php?id=<?php echo (int) $id_denunciado; ?>" class="link-voltar">← Voltar ao perfil</a>
        </div>
    </div>
</body>
</html>
