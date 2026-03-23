<?php
require_once 'ligaDB.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}

// Obter dados do utilizador da base de dados
$user_id = $_SESSION['user_id'];
$sql = "SELECT nome, email, telefone, biografia, foto_perfil FROM utilizador WHERE id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
$utilizador = $resultado->fetch_assoc();

$nome = $utilizador['nome'];
$email = $utilizador['email'];
$telefone = $utilizador['telefone'];
$biografia = $utilizador['biografia'];
$foto_perfil = resolve_profile_image($utilizador['foto_perfil'] ?? null);
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

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
$stmt_stats->bind_param("i", $user_id);
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
$stmt_animais->bind_param("i", $user_id);
$stmt_animais->execute();
$result_animais = $stmt_animais->get_result();
if ($result_animais) {
    while ($row_animal = $result_animais->fetch_assoc()) {
        $animais[] = $row_animal;
    }
}
$stmt_animais->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Save Animal Souls</title>
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

        .perfil-publico-foto-wrap {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .perfil-publico-foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #66BB6A;
        }

        .trocar-foto-btn {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 999px;
            background: #2D5016;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
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
    <!-- Navbar -->
    <?php include 'menu.php'; ?>

    <div class="perfil-publico-container">
        <div class="perfil-publico-header">
            <div class="perfil-publico-foto-wrap">
                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de Perfil" class="perfil-publico-foto">
                <button class="trocar-foto-btn" onclick="abrirModalFoto()" title="Trocar foto">📷</button>
            </div>
            <div>
                <h1 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($nome); ?></h1>
                <p style="margin: 6px 0; color: #666;"><?php echo htmlspecialchars($email); ?></p>
                <?php if (!empty($telefone)): ?>
                    <p style="margin: 6px 0; color: #666;">📱 <?php echo htmlspecialchars($telefone); ?></p>
                <?php endif; ?>

                <div class="perfil-publico-badges">
                    <?php if ($is_admin): ?>
                        <span class="badge-mini">Administrador</span>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="editar-perfil.php" class="btn btn-primary">Editar Perfil</a>
                    <a href="meus-animais.php" class="btn btn-secondary">Meus Animais</a>
                    <?php if ($is_admin): ?>
                        <a href="admin-dashboard.php" class="btn btn-secondary">Painel Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-danger">Terminar Sessão</a>
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
            <?php if (!empty($biografia)): ?>
                <p style="margin-top: 10px; color: #555; line-height: 1.7;"><?php echo nl2br(htmlspecialchars($biografia)); ?></p>
            <?php else: ?>
                <p style="margin-top: 10px; color: #777;">Ainda não adicionaste uma biografia. Clica em "Editar Perfil" para preencher.</p>
            <?php endif; ?>
        </div>

        <div class="perfil-animais">
            <h3 style="margin: 0; color: #2c3e50;">Os teus últimos animais</h3>
            <?php if (count($animais) > 0): ?>
                <div class="perfil-animais-lista">
                    <?php foreach ($animais as $animal): ?>
                        <a href="detalhes-animal.php?id=<?php echo (int) $animal['id_animal']; ?>" class="perfil-animal-item">
                            <img src="<?php echo htmlspecialchars(resolve_animal_image($animal['foto_animal'])); ?>" alt="<?php echo htmlspecialchars($animal['nome_animal']); ?>">
                            <div class="conteudo">
                                <strong><?php echo htmlspecialchars($animal['nome_animal']); ?></strong>
                                <div class="estado"><?php echo htmlspecialchars($animal['especie']); ?> • <?php echo ((int) $animal['adotado'] === 1) ? 'Adotado' : 'Disponível'; ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #777; margin-top: 10px;">Ainda não publicaste animais.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para trocar foto -->
    <div id="modalFoto" class="modal">
        <div class="modal-conteudo">
            <span class="fechar-modal" onclick="fecharModalFoto()">&times;</span>
            <h2>Trocar Foto de Perfil</h2>
            <form action="upload-foto.php" method="POST" enctype="multipart/form-data">
                <div class="grupo-input">
                    <label for="foto">Escolher nova foto:</label>
                    <input type="file" name="foto" id="foto" accept="image/*" required style="margin: 15px 0; width: 100%;">
                    <p style="font-size: 13px; color: #666;">Formatos aceites: JPG, PNG, GIF (máx. 5MB)</p>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 15px;">Enviar Foto</button>
            </form>
        </div>
    </div>

    <script>
        function abrirModalFoto() {
            document.getElementById('modalFoto').classList.add('ativo');
        }

        function fecharModalFoto() {
            document.getElementById('modalFoto').classList.remove('ativo');
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('modalFoto');
            if (event.target == modal) {
                fecharModalFoto();
            }
        }
    </script>
</body>
</html>