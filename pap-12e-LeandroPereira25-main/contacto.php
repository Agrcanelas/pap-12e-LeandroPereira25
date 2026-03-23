<?php require_once 'ligaDB.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Save Animal Souls</title>
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
        .pagina-box h1 { color: #2D5016; margin-bottom: 14px; }
        .linha { margin: 10px 0; color: #505050; }
        .acoes { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .btn-page {
            display: inline-block;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #4A6FA5 0%, #5D84BD 100%);
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="pagina-box">
        <h1>Contacto</h1>
        <p class="linha">Estamos disponíveis para ajudar em dúvidas sobre adoção, doações e parcerias.</p>
        <p class="linha"><strong>Email:</strong> a10961@agrcanelas.com</p>
        <p class="linha"><strong>Telefone:</strong> +351 913 134 304</p>
        <p class="linha"><strong>Local:</strong> Porto, Portugal</p>

        <div class="acoes">
            <a class="btn-page" href="mailto:a10961@agrcanelas.com?subject=Contacto%20Save%20Animal%20Souls">Enviar Email</a>
            <?php if (isset($_SESSION['logado']) && $_SESSION['logado']): ?>
                <a class="btn-page" href="mensagens.php">Ir para Mensagens</a>
            <?php else: ?>
                <a class="btn-page" href="formlogin.php">Fazer Login para Mensagens</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
