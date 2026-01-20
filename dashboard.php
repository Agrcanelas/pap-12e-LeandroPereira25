<?php
require_once 'ligaDB.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}

// Obter dados do utilizador
$nome = $_SESSION['user_nome'];
$email = $_SESSION['user_email'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    
</head>
<body>
    <!-- Navbar -->
    <div class="barra-navegacao">
        <a href="index.php">Início</a>
        <a class="ativo" href="dashboard.php">Dashboard</a>
        <a href="#">Meus Animais</a>
        <a href="#">Doações</a>
        <a href="logout.php">Sair</a>
    </div>

    <div class="dashboard-container">
        <div class="welcome-box">
            <h1>Bem-vindo, <?php echo htmlspecialchars($nome); ?>! 🐾</h1>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <p>Esta é a sua área de membro da Save Animal Souls</p>
        </div>

        <div class="info-cards">
            <div class="info-card">
                <h3>📊 Estatísticas</h3>
                <p>Veja o impacto das suas contribuições e atividades na plataforma.</p>
            </div>
            
            <div class="info-card">
                <h3>🐕 Adoções</h3>
                <p>Acompanhe os animais que adotou ou está a acompanhar.</p>
            </div>
            
            <div class="info-card">
                <h3>💚 Doações</h3>
                <p>Histórico das suas contribuições e certificados.</p>
            </div>
            
            <div class="info-card">
                <h3>⚙️ Configurações</h3>
                <p>Gerir o seu perfil e preferências de conta.</p>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="logout.php" class="logout-btn">Terminar Sessão</a>
        </div>
    </div>
</body>
</html>