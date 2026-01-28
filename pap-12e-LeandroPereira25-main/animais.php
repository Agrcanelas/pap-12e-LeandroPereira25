<?php
require_once 'ligaDB.php';

// Filtros
$filtro_especie = isset($_GET['especie']) ? $_GET['especie'] : '';
$filtro_adotado = isset($_GET['adotado']) ? $_GET['adotado'] : '';

// Query para buscar animais
$sql = "SELECT a.*, u.nome as nome_dono, u.email as email_dono 
        FROM animal a 
        JOIN utilizador u ON a.id_utilizador = u.id_utilizador 
        WHERE 1=1";

if ($filtro_especie) {
    $sql .= " AND a.especie = '" . $conn->real_escape_string($filtro_especie) . "'";
}

if ($filtro_adotado === '0') {
    $sql .= " AND a.adotado = 0";
} elseif ($filtro_adotado === '1') {
    $sql .= " AND a.adotado = 1";
}

$sql .= " ORDER BY a.adotado ASC, a.data_criacao DESC";

$resultado = $conn->query($sql);
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
    <div class="barra-navegacao">
        <a href="index.php">Início</a>
        <a class="ativo" href="animais.php">Adotar</a>
        <?php if(isset($_SESSION['logado'])): ?>
            <a href="meus-animais.php">Minhas Listagens</a>
            <a href="dashboard.php">Conta</a>
            <a href="logout.php">Sair</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>

<div class="animais-container">
    <!-- Header -->
    <div class="animais-header">
        <h1>🐾 Animais para Adoção</h1>
        <?php if(isset($_SESSION['logado'])): ?>
            <a href="adicionar-animal.php" class="btn-adicionar">+ Adicionar Animal</a>
        <?php else: ?>
            <a href="login.php" class="btn-adicionar">Fazer Login para Adicionar</a>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <form class="filtros" method="GET">
        <label>Espécie:</label>
        <select name="especie" onchange="this.form.submit()">
            <option value="">Todas</option>
            <option value="Cão" <?php echo $filtro_especie == 'Cão' ? 'selected' : ''; ?>>Cães</option>
            <option value="Gato" <?php echo $filtro_especie == 'Gato' ? 'selected' : ''; ?>>Gatos</option>
            <option value="Outro" <?php echo $filtro_especie == 'Outro' ? 'selected' : ''; ?>>Outros</option>
        </select>
        <label>Status:</label>
        <select name="adotado" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="0" <?php echo $filtro_adotado === '0' ? 'selected' : ''; ?>>Disponíveis</option>
            <option value="1" <?php echo $filtro_adotado === '1' ? 'selected' : ''; ?>>Adotados</option>
        </select>
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
            <p>Não há animais com os filtros selecionados no momento.</p>
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
</script>

</body>
</html>