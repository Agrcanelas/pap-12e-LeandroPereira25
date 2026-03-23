<?php require_once 'ligaDB.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doar - Save Animal Souls</title>
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
        .acoes { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .btn-page {
            display: inline-block;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
        }
        .btn-page.sec { background: linear-gradient(135deg, #4A6FA5 0%, #5D84BD 100%); }
        ul.lista-ajuda { margin-left: 18px; color: #555; }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="pagina-box">
        <h1>Doar para a Save Animal Souls</h1>
        <p>A tua contribuição ajuda diretamente no resgate, alimentação, cuidados veterinários e preparação para adoção dos animais.</p>

        <h3>Como a tua doação ajuda</h3>
        <ul class="lista-ajuda">
            <li>Consultas e tratamentos veterinários</li>
            <li>Ração, areia e suplementos</li>
            <li>Vacinação e desparasitação</li>
            <li>Campanhas de adoção responsável</li>
        </ul>

        <div class="acoes">
            <a class="btn-page" href="mailto:a10961@agrcanelas.com?subject=Doacao%20para%20Save%20Animal%20Souls">Quero Doar Agora</a>
            <a class="btn-page sec" href="contacto.php">Falar com a Equipa</a>
        </div>
    </main>
</body>
</html>
