<?php require_once 'ligaDB.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apadrinhar - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .pagina-box {
            max-width: 900px;
            margin: 100px auto 40px;
            background: #fff;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
        }
        .pagina-box h1 { color: #2D5016; margin-bottom: 10px; }
        .pagina-box p { color: #4e4e4e; line-height: 1.7; margin-bottom: 14px; }
        .btn-page {
            display: inline-block;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="pagina-box">
        <h1>Apadrinhar um Animal</h1>
        <p>Ao apadrinhar, contribuis mensalmente para alimentação, cuidados e tratamentos de um animal enquanto ele aguarda adoção.</p>
        <p>É uma forma prática de ajudar mesmo quando não podes adotar neste momento.</p>

        <a class="btn-page" href="funcionalidade-indisponivel.php">Quero Apadrinhar</a>
    </main>
</body>
</html>
