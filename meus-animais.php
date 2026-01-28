<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM animal WHERE id_utilizador = ? ORDER BY adotado ASC, data_criacao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Listagens - SAS</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        /* Usar os mesmos estilos de animais.php */
        .animais-container {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 20px;
        }

        .animais-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .animais-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin: 0;
        }

        .btn-adicionar {
            background: #4CAF50;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-adicionar:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .grid-animais {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card-animal {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .card-animal:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .card-animal.adotado {
            opacity: 0.7;
        }

        .foto-animal {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .badge-adotado {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #f44336;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-disponivel {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-conteudo {
            padding: 20px;
        }

        .card-conteudo h3 {
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .info-animal {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 15px 0;
        }

        .tag {
            background: #f0f0f0;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            color: #666;
        }

        .descricao-animal {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin: 15px 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .acoes-animal {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-acao {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-editar {
            background: #FF9800;
            color: white;
        }

        .btn-remover {
            background: #f44336;
            color: white;
        }

        .btn-marcar {
            background: #4CAF50;
            color: white;
        }

        .btn-acao:hover {
            transform: scale(1.05);
        }

        .sem-animais {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .sem-animais h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .animais-container {
                margin-top: 80px;
            }

            .animais-header {
                flex-direction: column;
                text-align: center;
            }

            .grid-animais {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="barra-navegacao">
        <a href="index.php">Início</a>
        <a href="animais.php">Adotar</a>
        <a class="ativo" href="meus-animais.php">Minhas Listagens</a>
        <a href="dashboard.php">Conta</a>
        <a href="logout.php">Sair</a>
    </div>

    <div class="animais-container">
        <div class="animais-header">
            <h1>🐾 Minhas Listagens</h1>
            <a href="adicionar-animal.php" class="btn-adicionar">+ Adicionar Animal</a>
        </div>

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

                            <div class="acoes-animal">
                                <button onclick="editarAnimal(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-editar">Editar</button>
                                
                                <?php if(!$animal['adotado']): ?>
                                    <button onclick="marcarAdotado(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-marcar">Marcar Adotado</button>
                                <?php endif; ?>
                                
                                <button onclick="removerAnimal(<?php echo $animal['id_animal']; ?>)" class="btn-acao btn-remover">Remover</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="sem-animais">
                <h2>Você ainda não adicionou nenhum animal</h2>
                <p>Comece a ajudar animais criando sua primeira listagem!</p>
                <a href="adicionar-animal.php" class="btn-adicionar">Adicionar Primeiro Animal</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
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