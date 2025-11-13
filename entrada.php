<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: registo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
        }
        .container {
            margin-top: 50px;
        }
        img {
            width: 50%;
            border-radius: 10px;
        }
        .user-info {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .logout-btn{
            margin-top: 10px;
            display: inline-block;
            background-color: #ff4d4d;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;

        }
        .logout-btn:hover
        {
            background-color: #cc0000;
        }
        </style>
</head>
<body>
       <!-- Informação do Utilizador no topo direito -->
       <div class="user-info">
        <?php 
        echo "Código: " . $_SESSION["user"]["codutilizador"] . "<br>";
        echo "Nome: " . $_SESSION["user"]["nomeutilizador"];
        ?>
        <br>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    <div class="container">
        
        <h1>Bem-vindo ao Website</h1>
        <img src="imagens/entrada.jpg" alt="Imagem linda">
        <p>Estamos felizes por tê-lo connosco!</p>
    </div>
</body>
</html>