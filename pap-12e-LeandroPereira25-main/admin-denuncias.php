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

$sql_tabela = "CREATE TABLE IF NOT EXISTS denuncias (
    id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
    id_denunciante INT NOT NULL,
    id_denunciado INT NOT NULL,
    assunto VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    motivo VARCHAR(250) DEFAULT NULL,
    estado ENUM('pendente','em_analise','resolvida','rejeitada') NOT NULL DEFAULT 'pendente',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_denunciante) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
    FOREIGN KEY (id_denunciado) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
    INDEX idx_denunciante (id_denunciante),
    INDEX idx_denunciado (id_denunciado),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$conn->query($sql_tabela);

$conn->query("ALTER TABLE denuncias ADD COLUMN IF NOT EXISTS assunto VARCHAR(150) NOT NULL DEFAULT '' AFTER id_denunciado");
$conn->query("ALTER TABLE denuncias ADD COLUMN IF NOT EXISTS descricao TEXT NULL AFTER assunto");

$filtro_estado = trim((string) ($_GET['estado'] ?? ''));

$sql = "SELECT d.id_denuncia, d.assunto, d.descricao, d.motivo, d.estado, d.data_criacao,
               denunciante.nome AS nome_denunciante,
               denunciado.id_utilizador AS id_denunciado,
               denunciado.nome AS nome_denunciado
        FROM denuncias d
        JOIN utilizador denunciante ON denunciante.id_utilizador = d.id_denunciante
        JOIN utilizador denunciado ON denunciado.id_utilizador = d.id_denunciado";

$permitidos = ['pendente', 'em_analise', 'resolvida', 'rejeitada'];
if (in_array($filtro_estado, $permitidos, true)) {
    $sql .= " WHERE d.estado = ?";
}

$sql .= " ORDER BY d.data_criacao DESC";

$stmt = $conn->prepare($sql);
if (in_array($filtro_estado, $permitidos, true)) {
    $stmt->bind_param("s", $filtro_estado);
}
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denúncias - Admin</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="estilo-dashboard.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="dashboard-container">
        <div class="perfil-header">
            <h1>🚩 Denúncias</h1>
            <p class="email">Gestão de denúncias de utilizadores</p>
        </div>

        <?php if (isset($_GET['sucesso'])): ?>
            <div style="margin-bottom: 15px; padding: 12px 14px; border-radius: 8px; background: #e8f6ec; color: #2d7a39; border: 1px solid #b8e0c2;">
                <?php
                    if ($_GET['sucesso'] === 'apagada') {
                        echo 'Denúncia apagada com sucesso.';
                    } else {
                        echo 'Estado da denúncia atualizado com sucesso.';
                    }
                ?>
            </div>
        <?php elseif (isset($_GET['erro'])): ?>
            <div style="margin-bottom: 15px; padding: 12px 14px; border-radius: 8px; background: #fdecec; color: #b53a3a; border: 1px solid #f3c2c2;">
                <?php
                    if ($_GET['erro'] === 'apagar_nao_permitido') {
                        echo 'Só é possível apagar denúncias já resolvidas.';
                    } elseif ($_GET['erro'] === 'nao_encontrada') {
                        echo 'Denúncia não encontrada.';
                    } else {
                        echo 'Não foi possível atualizar a denúncia.';
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="perfil-info">
            <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <label for="estado" style="font-weight: 600;">Estado:</label>
                <select id="estado" name="estado" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd;">
                    <option value="">Todos</option>
                    <option value="pendente" <?php echo $filtro_estado === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="em_analise" <?php echo $filtro_estado === 'em_analise' ? 'selected' : ''; ?>>Em análise</option>
                    <option value="resolvida" <?php echo $filtro_estado === 'resolvida' ? 'selected' : ''; ?>>Resolvida</option>
                    <option value="rejeitada" <?php echo $filtro_estado === 'rejeitada' ? 'selected' : ''; ?>>Rejeitada</option>
                </select>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="admin-denuncias.php" class="btn btn-secondary">Limpar</a>
            </form>
        </div>

        <div class="perfil-info">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">ID</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Denunciante</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Denunciado</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Assunto</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Descrição</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Estado</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Data</th>
                            <th style="text-align: left; padding: 10px; border-bottom: 1px solid #e6e6e6;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($row = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;">#<?php echo (int) $row['id_denuncia']; ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo htmlspecialchars($row['nome_denunciante']); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><a href="perfil-utilizador.php?id=<?php echo (int) $row['id_denunciado']; ?>" style="color: #2D5016; text-decoration: none; font-weight: 600;"><?php echo htmlspecialchars($row['nome_denunciado']); ?></a></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo htmlspecialchars($row['assunto'] ?: 'Sem assunto'); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0; max-width: 300px;"><?php echo nl2br(htmlspecialchars($row['descricao'] ?: ($row['motivo'] ?: 'Sem descrição'))); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0; font-weight: 600;"><?php echo htmlspecialchars($row['estado']); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;"><?php echo date('d/m/Y H:i', strtotime($row['data_criacao'])); ?></td>
                                    <td style="padding: 10px; border-bottom: 1px solid #f0f0f0;">
                                        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                            <a href="admin-denuncia-estado.php?id=<?php echo (int) $row['id_denuncia']; ?>&estado=em_analise" class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;">Em análise</a>
                                            <a href="admin-denuncia-estado.php?id=<?php echo (int) $row['id_denuncia']; ?>&estado=resolvida" class="btn btn-primary" style="padding: 6px 10px; font-size: 12px;">Resolver</a>
                                            <a href="admin-denuncia-estado.php?id=<?php echo (int) $row['id_denuncia']; ?>&estado=rejeitada" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;">Rejeitar</a>
                                            <?php if (($row['estado'] ?? '') === 'resolvida'): ?>
                                                <a href="admin-denuncia-apagar.php?id=<?php echo (int) $row['id_denuncia']; ?>" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;" onclick="return confirm('Apagar este registo de denúncia?')">Lixo</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="padding: 16px; color: #777;">Nenhuma denúncia encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
