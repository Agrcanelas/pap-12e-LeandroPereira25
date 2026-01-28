<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo-login.css">
</head>
<body>
    <div class="container-login">
        <div class="caixa-login">
            <div class="logo">
                <h1>SAS</h1>
                <p>Save Animal Souls</p>
            </div>

            <h2>Iniciar Sessão</h2>

            <?php
            // Mostrar mensagens de erro/sucesso
            if(isset($_GET['erro'])) {
                echo '<div class="mensagem erro">';
                if($_GET['erro'] == 'credenciais') {
                    echo 'Email ou password incorretos!';
                } elseif($_GET['erro'] == 'vazio') {
                    echo 'Por favor, preencha todos os campos!';
                }
                echo '</div>';
            }
            if(isset($_GET['sucesso']) && $_GET['sucesso'] == 'registo') {
                echo '<div class="mensagem sucesso">Registo realizado com sucesso! Faça login.</div>';
            }
            ?>

            <form action="login.php" method="POST">
                <div class="grupo-input">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="seuemail@exemplo.com">
                </div>

                <div class="grupo-input">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>


                <button type="submit" class="botao-login">Entrar</button>
            </form>

            <div class="divisor">
                <span>OU</span>
            </div>

            <p class="texto-registo">
                Ainda não tem conta? <a href="formregisto.php">Registar aqui</a>
            </p>

            <a href="index.php" class="link-voltar">← Voltar à Página Inicial</a>
        </div>
    </div>
</body>
</html>