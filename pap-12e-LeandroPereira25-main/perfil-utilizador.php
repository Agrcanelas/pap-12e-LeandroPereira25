<?php
require_once 'ligaDB.php';

$id_perfil = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id_perfil <= 0) {
    header('Location: animais.php');
    exit();
}

$sql_user = "SELECT id_utilizador, nome, email, telefone, biografia, foto_perfil, data_registo, ativo, role FROM utilizador WHERE id_utilizador = ? LIMIT 1";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_perfil);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if (!$result_user || $result_user->num_rows === 0) {
    header('Location: animais.php?erro=perfil_nao_encontrado');
    exit();
}

$perfil = $result_user->fetch_assoc();
$stmt_user->close();

$stats = [
    'publicados' => 0,
    'adotados' => 0,
    'disponiveis' => 0,
];

$sql_stats = "SELECT
                COUNT(*) AS publicados,
                SUM(CASE WHEN adotado = 1 THEN 1 ELSE 0 END) AS adotados,
                SUM(CASE WHEN adotado = 0 THEN 1 ELSE 0 END) AS disponiveis
              FROM animal
              WHERE id_utilizador = ?";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("i", $id_perfil);
$stmt_stats->execute();
$result_stats = $stmt_stats->get_result();
if ($result_stats && ($row_stats = $result_stats->fetch_assoc())) {
    $stats['publicados'] = (int) ($row_stats['publicados'] ?? 0);
    $stats['adotados'] = (int) ($row_stats['adotados'] ?? 0);
    $stats['disponiveis'] = (int) ($row_stats['disponiveis'] ?? 0);
}
$stmt_stats->close();

$animais = [];
$sql_animais = "SELECT id_animal, nome_animal, especie, foto_animal, adotado
               FROM animal
               WHERE id_utilizador = ?
               ORDER BY data_criacao DESC
               LIMIT 6";
$stmt_animais = $conn->prepare($sql_animais);
$stmt_animais->bind_param("i", $id_perfil);
$stmt_animais->execute();
$result_animais = $stmt_animais->get_result();
if ($result_animais) {
    while ($row_animal = $result_animais->fetch_assoc()) {
        $animais[] = $row_animal;
    }
}
$stmt_animais->close();

$data_registo = !empty($perfil['data_registo']) ? date('d/m/Y', strtotime($perfil['data_registo'])) : '-';
$ativo = !isset($perfil['ativo']) || (int) $perfil['ativo'] === 1;
$is_admin = isset($_SESSION['logado']) && $_SESSION['logado'] === true && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$is_own_profile = isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $perfil['id_utilizador'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($perfil['nome']); ?> - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="estilo-dashboard.css">
    <style>
        .perfil-publico-container {
            max-width: 1000px;
            margin: 100px auto 40px;
            padding: 20px;
        }

        .perfil-publico-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .perfil-publico-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #66BB6A;
        }

        .perfil-publico-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .badge-mini {
            background: #eef7ef;
            color: #2d7a39;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-inativo {
            background: #fdecec;
            color: #b53a3a;
        }

        .perfil-publico-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .perfil-stat {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 16px;
            text-align: center;
        }

        .perfil-stat .valor {
            font-size: 30px;
            font-weight: 700;
            color: #2D5016;
        }

        .perfil-animais {
            margin-top: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            padding: 20px;
        }

        .perfil-animais-lista {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 12px;
        }

        .perfil-animal-item {
            border: 1px solid #efefef;
            border-radius: 10px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            background: #fff;
        }

        .perfil-animal-item img {
            width: 100%;
            height: 170px;
            object-fit: contain;
            object-position: center;
            display: block;
            background: #f6f8fa;
        }

        .perfil-animal-item .conteudo {
            padding: 10px;
        }

        .perfil-animal-item .estado {
            font-size: 12px;
            font-weight: 600;
            color: #666;
        }

        @media (max-width: 768px) {
            .perfil-publico-container {
                margin-top: 85px;
            }

            .perfil-publico-header {
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="perfil-publico-container">
        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'denuncia_enviada'): ?>
            <div style="margin-bottom: 15px; padding: 12px 14px; border-radius: 8px; background: #e8f6ec; color: #2d7a39; border: 1px solid #b8e0c2;">
                Denúncia enviada com sucesso. A equipa admin irá analisar.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div style="margin-bottom: 15px; padding: 12px 14px; border-radius: 8px; background: #fdecec; color: #b53a3a; border: 1px solid #f3c2c2;">
                <?php
                    if ($_GET['erro'] === 'denuncia_existente') {
                        echo 'Já existe uma denúncia tua em análise para este perfil.';
                    } elseif ($_GET['erro'] === 'denuncia_erro') {
                        echo 'Não foi possível enviar a denúncia.';
                    } else {
                        echo 'Ocorreu um erro ao processar a denúncia.';
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="perfil-publico-header">
            <img src="<?php echo htmlspecialchars(resolve_profile_image($perfil['foto_perfil'] ?? null)); ?>" alt="<?php echo htmlspecialchars($perfil['nome']); ?>" class="perfil-publico-foto">
            <div>
                <h1 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($perfil['nome']); ?></h1>
                <p style="margin: 6px 0; color: #666;"><?php echo htmlspecialchars($perfil['email']); ?></p>
                <?php if (!empty($perfil['telefone'])): ?>
                    <p style="margin: 6px 0; color: #666;">📱 <?php echo htmlspecialchars($perfil['telefone']); ?></p>
                <?php endif; ?>
                <div class="perfil-publico-badges">
                    <span class="badge-mini">Membro desde <?php echo $data_registo; ?></span>
                    <?php if (!$ativo): ?>
                        <span class="badge-mini badge-inativo">Conta inativa</span>
                    <?php endif; ?>
                    <?php if (isset($perfil['role']) && $perfil['role'] === 'admin'): ?>
                        <span class="badge-mini">Administrador</span>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php if (isset($_SESSION['logado']) && $_SESSION['logado'] && isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] !== (int) $perfil['id_utilizador']): ?>
                        <a href="mensagens.php?com=<?php echo (int) $perfil['id_utilizador']; ?>" class="btn btn-primary">Enviar Mensagem</a>
                        <a href="denunciar-perfil.php?id=<?php echo (int) $perfil['id_utilizador']; ?>" class="btn btn-danger">Denunciar</a>
                    <?php endif; ?>
                    <?php if ($is_admin && !$is_own_profile): ?>
                        <?php if ($ativo): ?>
                            <a href="admin-utilizador-toggle.php?id=<?php echo (int) $perfil['id_utilizador']; ?>&acao=inativar" class="btn btn-danger" onclick="return confirm('Desativar esta conta?')">Desativar Conta</a>
                        <?php else: ?>
                            <a href="admin-utilizador-toggle.php?id=<?php echo (int) $perfil['id_utilizador']; ?>&acao=ativar" class="btn btn-primary">Reativar Conta</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="animais.php" class="btn btn-secondary">← Voltar aos Animais</a>
                </div>
            </div>
        </div>

        <div class="perfil-publico-grid">
            <div class="perfil-stat">
                <div class="valor"><?php echo $stats['publicados']; ?></div>
                <div>Animais Publicados</div>
            </div>
            <div class="perfil-stat">
                <div class="valor"><?php echo $stats['disponiveis']; ?></div>
                <div>Disponíveis</div>
            </div>
            <div class="perfil-stat">
                <div class="valor"><?php echo $stats['adotados']; ?></div>
                <div>Já Adotados</div>
            </div>
        </div>

        <div class="perfil-animais">
            <h3 style="margin: 0; color: #2c3e50;">Sobre</h3>
            <?php if (!empty($perfil['biografia'])): ?>
                <p style="margin-top: 10px; color: #555; line-height: 1.7;"><?php echo nl2br(htmlspecialchars($perfil['biografia'])); ?></p>
            <?php else: ?>
                <p style="margin-top: 10px; color: #777;">Este utilizador ainda não adicionou uma biografia.</p>
            <?php endif; ?>
        </div>

        <div class="perfil-animais">
            <h3 style="margin: 0; color: #2c3e50;">Últimos animais deste utilizador</h3>
            <?php if (count($animais) > 0): ?>
                <div class="perfil-animais-lista">
                    <?php foreach ($animais as $animal): ?>
                        <a href="detalhes-animal.php?id=<?php echo (int) $animal['id_animal']; ?>" class="perfil-animal-item">
                            <img src="<?php echo htmlspecialchars(resolve_animal_image($animal['foto_animal'])); ?>" alt="<?php echo htmlspecialchars($animal['nome_animal']); ?>">
                            <div class="conteudo">
                                <strong><?php echo htmlspecialchars($animal['nome_animal']); ?></strong>
                                <div class="estado"><?php echo htmlspecialchars($animal['especie']); ?> • <?php echo ((int)$animal['adotado'] === 1) ? 'Adotado' : 'Disponível'; ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #777; margin-top: 10px;">Este utilizador ainda não publicou animais.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
