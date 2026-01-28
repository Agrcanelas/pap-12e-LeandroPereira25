<?php
require_once 'ligaDB.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.html");
    exit();
}

// Obter dados do utilizador da base de dados
$user_id = $_SESSION['user_id'];
$sql = "SELECT nome, email, telefone, biografia, foto_perfil, data_registo FROM utilizador WHERE id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
$utilizador = $resultado->fetch_assoc();

$nome = $utilizador['nome'];
$email = $utilizador['email'];
$telefone = $utilizador['telefone'];
$biografia = $utilizador['biografia'];
$foto_perfil = $utilizador['foto_perfil'] ?? 'uploads/default-avatar.png';
$data_registo = date('d/m/Y', strtotime($utilizador['data_registo']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .dashboard-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 20px;
        }

        .perfil-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
        }

        .foto-perfil-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .foto-perfil {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #4CAF50;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .trocar-foto-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .trocar-foto-btn:hover {
            background: #45a049;
            transform: scale(1.1);
        }

        .perfil-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 5px;
        }

        .perfil-header .email {
            color: #666;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .badge {
            display: inline-block;
            padding: 5px 15px;
            background: #e8f5e9;
            color: #4CAF50;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .perfil-info {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }

        .info-secao {
            margin-bottom: 25px;
        }

        .info-secao h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-linha {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-linha:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-valor {
            color: #2c3e50;
        }

        .biografia-texto {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            color: #555;
            line-height: 1.6;
            min-height: 80px;
        }

        .biografia-texto.vazia {
            color: #999;
            font-style: italic;
        }

        .botoes-acao {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #4CAF50;
            color: white;
        }

        .btn-primary:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-secondary {
            background: #2c3e50;
            color: white;
        }

        .btn-secondary:hover {
            background: #34495e;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        /* Modal para trocar foto */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal.ativo {
            display: flex;
        }

        .modal-conteudo {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
        }

        .modal-conteudo h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .fechar-modal {
            float: right;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .fechar-modal:hover {
            color: #000;
        }

        .grupo-input {
            margin-bottom: 15px;
        }

        .grupo-input label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .grupo-input input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                margin-top: 80px;
            }

            .perfil-header {
                padding: 30px 20px;
            }

            .foto-perfil, .foto-perfil-container {
                width: 120px;
                height: 120px;
            }

            .botoes-acao {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="barra-navegacao">
        <a href="index.php">Início</a>
        <a class="ativo" href="dashboard.php">Conta</a>
        <a href="animais.php">Adotar</a>
        <a href="meus-animais.php">Minhas Listagens</a>
        <a href="logout.php">Sair</a>
    </div>

    <div class="dashboard-container">
        <!-- Header do Perfil -->
        <div class="perfil-header">
            <div class="foto-perfil-container">
                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de Perfil" class="foto-perfil">
                <button class="trocar-foto-btn" onclick="abrirModalFoto()" title="Trocar foto">📷</button>
            </div>
            <h1><?php echo htmlspecialchars($nome); ?></h1>
            <p class="email"><?php echo htmlspecialchars($email); ?></p>
            <span class="badge">Membro desde <?php echo $data_registo; ?></span>
        </div>

        <!-- Informações do Perfil -->
        <div class="perfil-info">
            <div class="info-secao">
                <h3>📋 Informações Pessoais</h3>
                <div class="info-linha">
                    <span class="info-label">Nome:</span>
                    <span class="info-valor"><?php echo htmlspecialchars($nome); ?></span>
                </div>
                <div class="info-linha">
                    <span class="info-label">Email:</span>
                    <span class="info-valor"><?php echo htmlspecialchars($email); ?></span>
                </div>
                <div class="info-linha">
                    <span class="info-label">Telefone:</span>
                    <span class="info-valor"><?php echo htmlspecialchars($telefone ?: 'Não informado'); ?></span>
                </div>
            </div>

            <div class="info-secao">
                <h3>✍️ Biografia</h3>
                <div class="biografia-texto <?php echo empty($biografia) ? 'vazia' : ''; ?>">
                    <?php echo empty($biografia) ? 'Nenhuma biografia adicionada ainda. Clique em "Editar Perfil" para adicionar.' : nl2br(htmlspecialchars($biografia)); ?>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="botoes-acao">
            <a href="editar-perfil.php" class="btn btn-primary">✏️ Editar Perfil</a>
            <a href="alterar-password.php" class="btn btn-secondary">🔒 Alterar Password</a>
            <a href="logout.php" class="btn btn-danger">🚪 Terminar Sessão</a>
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
                    <input type="file" name="foto" id="foto" accept="image/*" required>
                    <p style="font-size: 13px; color: #666; margin-top: 10px;">Formatos aceites: JPG, PNG, GIF (máx. 5MB)</p>
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