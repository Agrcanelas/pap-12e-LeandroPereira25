<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashboard.php?erro=sem_permissao');
    exit();
}

$stats = [
    'utilizadores' => 0,
    'animais_total' => 0,
    'animais_adotados' => 0,
    'mensagens' => 0,
];

$resultado = $conn->query("SELECT COUNT(*) AS total FROM utilizador");
if ($resultado && ($linha = $resultado->fetch_assoc())) {
    $stats['utilizadores'] = (int) $linha['total'];
}

$resultado = $conn->query("SELECT COUNT(*) AS total FROM animal");
if ($resultado && ($linha = $resultado->fetch_assoc())) {
    $stats['animais_total'] = (int) $linha['total'];
}

$resultado = $conn->query("SELECT COUNT(*) AS total FROM animal WHERE adotado = 1");
if ($resultado && ($linha = $resultado->fetch_assoc())) {
    $stats['animais_adotados'] = (int) $linha['total'];
}

$resultado = $conn->query("SELECT COUNT(*) AS total FROM mensagem");
if ($resultado && ($linha = $resultado->fetch_assoc())) {
    $stats['mensagens'] = (int) $linha['total'];
}

$utilizadores = [];
$sql_utilizadores = "SELECT id_utilizador, nome, email, role, ativo, data_registo FROM utilizador ORDER BY data_registo DESC";
$resultado = $conn->query($sql_utilizadores);
if ($resultado) {
    while ($linha = $resultado->fetch_assoc()) {
        $utilizadores[] = $linha;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="estilo-dashboard.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="dashboard-container">
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'estado_utilizador'): ?>
            <div style="margin-bottom: 15px; padding: 12px 14px; border-radius: 8px; background: #e8f6ec; color: #2d7a39; border: 1px solid #b8e0c2;">
                Estado do utilizador atualizado com sucesso.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div style="margin-bottom: 15px; padding: 12px 14px; border-radius: 8px; background: #fdecec; color: #b53a3a; border: 1px solid #f3c2c2;">
                <?php
                    if ($_GET['erro'] === 'auto_inativar') {
                        echo 'Não podes inativar a tua própria conta de admin.';
                    } elseif ($_GET['erro'] === 'estado_utilizador') {
                        echo 'Não foi possível atualizar o estado do utilizador.';
                    } else {
                        echo 'Ocorreu um erro ao processar a ação.';
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="perfil-header">
            <h1>🛠️ Painel de Administração</h1>
            <p class="email">Área exclusiva para utilizadores admin</p>
            <span class="badge">Sessão de <?php echo htmlspecialchars($_SESSION['user_nome']); ?></span>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <h3>Utilizadores</h3>
                <div class="valor"><?php echo $stats['utilizadores']; ?></div>
            </div>
            <div class="admin-card">
                <h3>Animais Publicados</h3>
                <div class="valor"><?php echo $stats['animais_total']; ?></div>
            </div>
            <div class="admin-card">
                <h3>Animais Adotados</h3>
                <div class="valor"><?php echo $stats['animais_adotados']; ?></div>
            </div>
            <div class="admin-card">
                <h3>Mensagens</h3>
                <div class="valor"><?php echo $stats['mensagens']; ?></div>
            </div>
        </div>

        <div class="perfil-info">
            <div class="info-secao">
                <h3>⚡ Ações rápidas</h3>
                <div class="botoes-acao" style="justify-content: flex-start;">
                    <a href="animais.php" class="btn btn-primary">Ver Animais</a>
                    <a href="animais-adotados.php" class="btn btn-secondary">Ver Adotados</a>
                    <a href="mensagens.php" class="btn btn-primary">Ver Mensagens</a>
                    <a href="admin-denuncias.php" class="btn btn-danger">Ver Denúncias</a>
                    <a href="dashboard.php" class="btn btn-secondary">Voltar ao Perfil</a>
                </div>
            </div>
        </div>

        <div class="perfil-info">
            <div class="info-secao">
                <h3>👥 Gestão de Utilizadores</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Nome</th>
                                <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Email</th>
                                <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Role</th>
                                <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Estado</th>
                                <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilizadores as $utilizador): ?>
                                <?php $ativo = isset($utilizador['ativo']) ? (int) $utilizador['ativo'] === 1 : true; ?>
                                <tr>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo htmlspecialchars($utilizador['nome']); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo htmlspecialchars($utilizador['email']); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo htmlspecialchars($utilizador['role'] ?? 'user'); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0; color: <?php echo $ativo ? '#2d7a39' : '#b53a3a'; ?>; font-weight: 600;">
                                        <?php echo $ativo ? 'Ativo' : 'Inativo'; ?>
                                    </td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                        <?php if ((int)$utilizador['id_utilizador'] === (int)$_SESSION['user_id']): ?>
                                            <span style="color: #999;">Conta atual</span>
                                        <?php elseif ($ativo): ?>
                                            <a href="admin-utilizador-toggle.php?id=<?php echo (int)$utilizador['id_utilizador']; ?>&acao=inativar" class="btn btn-danger" style="padding: 7px 12px; font-size: 13px;" onclick="return confirm('Inativar este utilizador?')">Inativar</a>
                                        <?php else: ?>
                                            <a href="admin-utilizador-toggle.php?id=<?php echo (int)$utilizador['id_utilizador']; ?>&acao=ativar" class="btn btn-primary" style="padding: 7px 12px; font-size: 13px;">Ativar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
