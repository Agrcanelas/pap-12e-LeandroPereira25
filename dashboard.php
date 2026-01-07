<?php
require_once 'ligaDB.php';

// Verificar se o utilizador está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.html");
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
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 40px 20px;
        }
        
        .welcome-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 40px;
        }
        
        .welcome-box h1 {
            color: #4CAF50;
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .welcome-box p {
            color: #666;
            font-size: 18px;
        }
        
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }
        
        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .info-card p {
            color: #666;
        }
        
        .logout-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        
        .logout-btn:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="barra-navegacao">
        <a href="index.html">Início</a>
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