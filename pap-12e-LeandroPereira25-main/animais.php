<?php
require_once 'ligaDB.php';

// Filtros
$filtro_especie = isset($_GET['especie']) ? $_GET['especie'] : '';
$filtro_nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';

// Query para buscar animais
$sql = "SELECT a.*, u.nome as nome_dono, u.email as email_dono 
        FROM animal a 
        JOIN utilizador u ON a.id_utilizador = u.id_utilizador 
    WHERE a.adotado = 0";

if ($filtro_especie) {
    $sql .= " AND a.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
}

if ($filtro_nome !== '') {
    $sql .= " AND a.nome_animal LIKE '%" . $conn->real_escape_string($filtro_nome) . "%'";
}

$sql .= " ORDER BY a.data_criacao DESC";

$resultado = $conn->query($sql);

// Buscar favoritos do utilizador logado
$favoritos = array();
if (isset($_SESSION['logado']) && isset($_SESSION['user_id'])) {
    $sql_fav = "SELECT id_animal FROM favoritos WHERE id_utilizador = ?";
    $stmt = $conn->prepare($sql_fav);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $resultado_fav = $stmt->get_result();
    
    while ($fav = $resultado_fav->fetch_assoc()) {
        $favoritos[$fav['id_animal']] = true;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animais para Adoção - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <link rel="stylesheet" href="estilo-animais.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'menu.php'; ?>

<div class="animais-container">
    <!-- Header -->
    <div class="animais-header">
        <h1>🐾 Animais Disponíveis para Adoção</h1>
        <?php if(isset($_SESSION['logado'])): ?>
            <a href="adicionar-animal.php" class="btn-adicionar">+ Adicionar Animal</a>
        <?php else: ?>
            <a href="login.php" class="btn-adicionar">Fazer Login para Adicionar</a>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <form class="filtros" method="GET">
        <label>Nome:</label>
        <input type="search" name="nome" value="<?php echo htmlspecialchars($filtro_nome); ?>" placeholder="Ex: Max">
        <label>Espécie:</label>
        <select name="especie" onchange="this.form.submit()">
            <option value="">Todas</option>
            <option value="Cão" <?php echo $filtro_especie == 'Cão' ? 'selected' : ''; ?>>Cães</option>
            <option value="Gato" <?php echo $filtro_especie == 'Gato' ? 'selected' : ''; ?>>Gatos</option>
            <option value="Outro" <?php echo $filtro_especie == 'Outro' ? 'selected' : ''; ?>>Outros</option>
        </select>
        <button type="submit" class="btn-acao btn-ver" style="max-width: 140px;">Pesquisar</button>
        <a href="animais.php" style="color: #4CAF50; text-decoration: none; font-weight: 600;">Limpar Filtros</a>
    </form>

    <!-- Grid de Animais -->
    <?php if($resultado->num_rows > 0): ?>
        <div class="grid-animais">
            <?php while($animal = $resultado->fetch_assoc()): ?>
                <div class="card-animal <?php echo $animal['adotado'] ? 'adotado' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($animal['foto_animal'] ?: 'uploads/animal-default.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($animal['nome_animal']); ?>" 
                         class="foto-animal">
                    <?php if($animal['adotado']): ?>
                        <span class="badge-adotado">✓ Adotado</span>
                    <?php else: ?>
                        <span class="badge-disponivel">Disponível</span>
                    <?php endif; ?>
                    <div class="card-conteudo">
                        <h3><?php echo htmlspecialchars($animal['nome_animal']); ?></h3>
                        <div class="info-animal">
                            <span class="tag">🐕 <?php echo htmlspecialchars($animal['especie']); ?></span>
                            <span class="tag"><?php echo htmlspecialchars($animal['sexo']); ?></span>
                            <?php if($animal['idade']): ?>
                                <span class="tag">⏰ <?php echo htmlspecialchars($animal['idade']); ?></span>
                            <?php endif; ?>
                            <?php if($animal['porte']): ?>
                                <span class="tag">📏 <?php echo htmlspecialchars($animal['porte']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if($animal['descricao']): ?>
                            <p class="descricao-animal"><?php echo htmlspecialchars($animal['descricao']); ?></p>
                        <?php endif; ?>
                        <?php if($animal['localidade']): ?>
                            <p class="info-dono">📍 <?php echo htmlspecialchars($animal['localidade']); ?></p>
                        <?php endif; ?>
                        <p class="info-dono">Por: <?php echo htmlspecialchars($animal['nome_dono']); ?></p>
                        <div class="acoes-animal">
                            <button onclick="verDetalhes(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-ver">Ver Mais</button>
                            <?php if(isset($_SESSION['logado'])): ?>
                                <?php if(isset($favoritos[$animal['id_animal']])): ?>
                                    <button onclick="removerFavoritoAnimais(<?php echo $animal['id_animal']; ?>)" class="btn-checkmark" title="Remover dos favoritos">✓</button>
                                <?php else: ?>
                                    <a href="adicionar-favorito.php?id=<?php echo $animal['id_animal']; ?>" class="btn-acao btn-favorito" title="Adicionar aos favoritos">❤️ Favoritar</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['logado']) && $_SESSION['user_id'] == $animal['id_utilizador']): ?>
                                <button onclick="editarAnimal(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-editar">Editar</button>
                                <?php if(!$animal['adotado']): ?>
                                    <button onclick="marcarAdotado(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-marcar">Adotado</button>
                                <?php endif; ?>
                                <button onclick="removerAnimal(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-remover">Remover</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="sem-animais">
            <h2>😢 Nenhum animal encontrado</h2>
            <p>Não há animais disponíveis com os filtros selecionados no momento.</p>
            <?php if(isset($_SESSION['logado'])): ?>
                <a href="adicionar-animal.php" class="btn-adicionar">Adicionar o Primeiro Animal</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function verDetalhes(id) {
        window.location.href = 'detalhes-animal.php?id=' + id;
    }

    function editarAnimal(id) {
        window.location.href = 'editar-animal.php?id=' + id;
    }

    function marcarAdotado(id) {
        if(confirm('Marcar este animal como adotado?')) {
            window.location.href = 'marcar-adotado.php?id=' + id;
        }
    }

    function removerAnimal(id) {
        if(confirm('Tem certeza que deseja remover este animal? Esta ação não pode ser desfeita.')) {
            window.location.href = 'remover-animal.php?id=' + id;
        }
    }

    function removerFavoritoAnimais(id) {
        window.location.href = 'remover-favorito.php?id=' + id;
    }

</script>

</body>
</html>