<?php
require_once 'ligaDB.php';

$filtro_especie = isset($_GET['especie']) ? $_GET['especie'] : '';
$filtro_nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';

$sql = "SELECT a.*, u.nome as nome_dono
        FROM animal a
        JOIN utilizador u ON a.id_utilizador = u.id_utilizador
        WHERE a.adotado = 1";

if ($filtro_especie) {
    $sql .= " AND a.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
}

if ($filtro_nome !== '') {
    $sql .= " AND a.nome_animal LIKE '%" . $conn->real_escape_string($filtro_nome) . "%'";
}

$sql .= " ORDER BY a.data_criacao DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animais Adotados - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="estilo-animais.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="animais-container">
        <div class="animais-header">
            <h1>🎉 Animais Adotados</h1>
            <a href="animais.php" class="btn-adicionar">Ver Animais Disponiveis</a>
        </div>

        <form class="filtros" method="GET">
            <label>Nome:</label>
            <input type="search" name="nome" value="<?php echo htmlspecialchars($filtro_nome); ?>" placeholder="Ex: Luna">
            <label>Especie:</label>
            <select name="especie" onchange="this.form.submit()">
                <option value="">Todas</option>
                <option value="Cão" <?php echo $filtro_especie == 'Cão' ? 'selected' : ''; ?>>Caes</option>
                <option value="Gato" <?php echo $filtro_especie == 'Gato' ? 'selected' : ''; ?>>Gatos</option>
                <option value="Outro" <?php echo $filtro_especie == 'Outro' ? 'selected' : ''; ?>>Outros</option>
            </select>
            <button type="submit" class="btn-acao btn-ver" style="max-width: 140px;">Pesquisar</button>
            <a href="animais-adotados.php" style="color: #4CAF50; text-decoration: none; font-weight: 600;">Limpar Filtros</a>
        </form>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="grid-animais">
                <?php while ($animal = $resultado->fetch_assoc()): ?>
                    <div class="card-animal adotado">
                        <img src="<?php echo htmlspecialchars(resolve_animal_image($animal['foto_animal'])); ?>"
                             alt="<?php echo htmlspecialchars($animal['nome_animal']); ?>"
                             class="foto-animal">

                        <span class="badge-adotado">✓ Adotado</span>

                        <div class="card-conteudo">
                            <h3><?php echo htmlspecialchars($animal['nome_animal']); ?></h3>

                            <div class="info-animal">
                                <span class="tag">🐕 <?php echo htmlspecialchars($animal['especie']); ?></span>
                                <span class="tag"><?php echo htmlspecialchars($animal['sexo']); ?></span>
                                <?php if ($animal['idade']): ?>
                                    <span class="tag">⏰ <?php echo htmlspecialchars($animal['idade']); ?></span>
                                <?php endif; ?>
                                <?php if ($animal['porte']): ?>
                                    <span class="tag">📏 <?php echo htmlspecialchars($animal['porte']); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($animal['descricao']): ?>
                                <p class="descricao-animal"><?php echo htmlspecialchars($animal['descricao']); ?></p>
                            <?php endif; ?>

                            <?php if ($animal['localidade']): ?>
                                <p class="info-dono">📍 <?php echo htmlspecialchars($animal['localidade']); ?></p>
                            <?php endif; ?>
                            <p class="info-dono">Publicado por: <a href="perfil-utilizador.php?id=<?php echo (int) $animal['id_utilizador']; ?>" style="color: #2D5016; font-weight: 600; text-decoration: none;"><?php echo htmlspecialchars($animal['nome_dono']); ?></a></p>

                            <div class="acoes-animal">
                                <button onclick="verDetalhes(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-ver">Ver Mais</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="sem-animais">
                <h2>Ainda nao ha animais adotados</h2>
                <p>Quando uma adocao for concluida, vais ve-la aqui.</p>
                <a href="animais.php" class="btn-adicionar">Ver Animais Disponiveis</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function verDetalhes(id) {
            window.location.href = 'detalhes-animal.php?id=' + id;
        }
    </script>
</body>
</html>
