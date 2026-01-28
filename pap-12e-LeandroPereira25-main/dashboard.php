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
    <link rel="stylesheet" href="estilo-dashboard.css">
</head>
<body>
    <!-- Navbar -->
    <div class="barra-navegacao">
        <a href="index.php">Início</a>
        <a class="ativo" href="dashboard.php">Conta</a>
        <a href="#">Meus Animais</a>
        <a href="#">Adoptar</a>
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