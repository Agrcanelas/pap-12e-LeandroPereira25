<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Buscar favoritos do utilizador
$sql = "SELECT a.*, u.nome as nome_dono, u.email as email_dono
        FROM favoritos f
        JOIN animal a ON f.id_animal = a.id_animal
        JOIN utilizador u ON a.id_utilizador = u.id_utilizador
        WHERE f.id_utilizador = ?
        ORDER BY f.data_criacao DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
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

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="grid-animais">
                <?php while ($animal = $resultado->fetch_assoc()): ?>
                    <div class="card-animal">
                        <img src="<?php echo htmlspecialchars($animal['foto_animal'] ?: 'uploads/animal-default.jpg'); ?>"
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

                            <p class="info-dono">Por: <?php echo htmlspecialchars($animal['nome_dono']); ?></p>

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
