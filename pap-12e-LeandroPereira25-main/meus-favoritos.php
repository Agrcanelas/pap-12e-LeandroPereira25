<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$filtro_nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';

// Buscar favoritos do utilizador
$sql = "SELECT a.*, u.nome as nome_dono, u.email as email_dono
        FROM favoritos f
        JOIN animal a ON f.id_animal = a.id_animal
        JOIN utilizador u ON a.id_utilizador = u.id_utilizador
    WHERE f.id_utilizador = ?";

$tipos = "i";
$parametros = [$user_id];

if ($filtro_nome !== '') {
    $sql .= " AND a.nome_animal LIKE ?";
    $tipos .= "s";
    $parametros[] = "%" . $filtro_nome . "%";
}

$sql .= " ORDER BY f.data_criacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($tipos, ...$parametros);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Favoritos - SAS</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="estilo-animais.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="animais-container">
        <div class="animais-header">
            <h1>❤️ Meus Favoritos</h1>
            <a href="animais.php" class="btn-adicionar">Ver Todos os Animais</a>
        </div>

        <form class="filtros" method="GET">
            <label for="nome">Nome:</label>
            <input id="nome" type="search" name="nome" value="<?php echo htmlspecialchars($filtro_nome); ?>" placeholder="Ex: Mel">
            <button type="submit" class="btn-acao btn-ver" style="max-width: 140px;">Pesquisar</button>
            <a href="meus-favoritos.php" style="color: #4CAF50; text-decoration: none; font-weight: 600;">Limpar Filtros</a>
        </form>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="grid-animais">
                <?php while ($animal = $resultado->fetch_assoc()): ?>
                    <div class="card-animal">
                        <img src="<?php echo htmlspecialchars(resolve_animal_image($animal['foto_animal'])); ?>"
                             alt="<?php echo htmlspecialchars($animal['nome_animal']); ?>"
                             class="foto-animal">
                        
                        <span class="badge-disponivel">Disponível</span>

                        <div class="card-conteudo">
                            <h3><?php echo htmlspecialchars($animal['nome_animal']); ?></h3>

                            <div class="info-animal">
                                <span class="tag">🐕 <?php echo htmlspecialchars($animal['especie']); ?></span>
                                <span class="tag"><?php echo htmlspecialchars($animal['sexo']); ?></span>
                                <?php if ($animal['idade']): ?>
                                    <span class="tag">⏰ <?php echo htmlspecialchars($animal['idade']); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($animal['descricao']): ?>
                                <p class="descricao-animal"><?php echo htmlspecialchars($animal['descricao']); ?></p>
                            <?php endif; ?>

                            <p class="info-dono">Por: <a href="perfil-utilizador.php?id=<?php echo (int) $animal['id_utilizador']; ?>" style="color: #2D5016; font-weight: 600; text-decoration: none;"><?php echo htmlspecialchars($animal['nome_dono']); ?></a></p>

                            <div class="acoes-animal">
                                <button onclick="verDetalhes(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-ver">Ver Mais</button>
                                <button onclick="removerFavorito(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-remover">Remover</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="sem-animais">
                <h2>Nenhum favorito ainda</h2>
                <p>Adiciona animais aos favoritos para vê-los aqui mais tarde!</p>
                <a href="animais.php" class="btn-adicionar">Ver Animais Disponíveis</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function verDetalhes(id) {
            window.location.href = 'detalhes-animal.php?id=' + id;
        }

        function removerFavorito(id) {
            if (confirm('Remover este animal dos favoritos?')) {
                window.location.href = 'remover-favorito.php?id=' + id;
            }
        }
    </script>
</body>
</html>
