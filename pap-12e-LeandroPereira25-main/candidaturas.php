<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Buscar todas as candidaturas para animais do utilizador
$sql = "SELECT c.*, a.nome_animal, a.foto_animal, u.nome as candidato_nome, u.email as candidato_email
        FROM candidatura_adocao c
        JOIN animal a ON c.id_animal = a.id_animal
        JOIN utilizador u ON c.id_utilizador = u.id_utilizador
        WHERE a.id_utilizador = ?
        ORDER BY c.status DESC, c.data_candidatura DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidaturas para Adoção - SAS</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .candidaturas-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 20px;
        }

        .candidaturas-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .candidaturas-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin: 0;
        }

        .candidatura-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .candidatura-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .candidatura-header {
            background: linear-gradient(135deg, #2D5016 0%, #3A6B35 100%);
            color: white;
            padding: 20px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 20px;
            align-items: center;
        }

        .candidatura-foto {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid white;
        }

        .candidatura-titulo {
            flex: 1;
        }

        .candidatura-titulo h3 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: 700;
        }

        .candidatura-titulo p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }

        .status-pendente {
            background: #FFB74D;
            color: white;
        }

        .status-aceite {
            background: #66BB6A;
            color: white;
        }

        .status-rejeitada {
            background: #FF7043;
            color: white;
        }

        .candidatura-conteudo {
            padding: 25px;
        }

        .candidatura-detalhes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .detalhe-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #66BB6A;
        }

        .detalhe-label {
            color: #999;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .detalhe-valor {
            color: #2C2C2C;
            font-size: 14px;
        }

        .candidatura-motivo {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #2196F3;
            margin-bottom: 20px;
        }

        .candidatura-motivo h4 {
            color: #2D5016;
            margin-top: 0;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .candidatura-motivo p {
            color: #555;
            margin: 8px 0;
            line-height: 1.6;
        }

        .candidatura-acoes {
            display: flex;
            gap: 10px;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }

        .btn-acao-candidatura {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
        }

        .btn-aceitar {
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
            color: white;
        }

        .btn-rejeitar {
            background: #FF7043;
            color: white;
        }

        .btn-mensagem {
            background: #2196F3;
            color: white;
        }

        .btn-acao-candidatura:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .sem-candidaturas {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .sem-candidaturas h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .candidatura-header {
                grid-template-columns: 1fr;
            }

            .candidatura-detalhes {
                grid-template-columns: 1fr;
            }

            .candidatura-acoes {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="candidaturas-container">
        <div class="candidaturas-header">
            <h1>📋 Candidaturas para Adoção</h1>
        </div>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($candidatura = $resultado->fetch_assoc()): ?>
                <div class="candidatura-card">
                    <div class="candidatura-header">
                        <img src="<?php echo htmlspecialchars($candidatura['foto_animal'] ?: 'uploads/animal-default.jpg'); ?>"
                             alt="<?php echo htmlspecialchars($candidatura['nome_animal']); ?>"
                             class="candidatura-foto">

                        <div class="candidatura-titulo">
                            <h3><?php echo htmlspecialchars($candidatura['nome_animal']); ?></h3>
                            <p>Candidato: <strong><?php echo htmlspecialchars($candidatura['candidato_nome']); ?></strong></p>
                        </div>

                        <span class="status-badge status-<?php echo htmlspecialchars($candidatura['status']); ?>">
                            <?php echo strtoupper($candidatura['status']); ?>
                        </span>
                    </div>

                    <div class="candidatura-conteudo">
                        <div class="candidatura-detalhes">
                            <div class="detalhe-item">
                                <div class="detalhe-label">📍 Situação Habitacional</div>
                                <div class="detalhe-valor"><?php echo htmlspecialchars($candidatura['situacao_habitacional']); ?></div>
                            </div>

                            <div class="detalhe-item">
                                <div class="detalhe-label">📞 Contacto</div>
                                <div class="detalhe-valor"><?php echo htmlspecialchars($candidatura['dados_contacto']); ?></div>
                            </div>

                            <div class="detalhe-item">
                                <div class="detalhe-label">✉️ Email</div>
                                <div class="detalhe-valor">
                                    <a href="mailto:<?php echo htmlspecialchars($candidatura['candidato_email']); ?>" style="color: #2196F3; text-decoration: none;">
                                        <?php echo htmlspecialchars($candidatura['candidato_email']); ?>
                                    </a>
                                </div>
                            </div>

                            <div class="detalhe-item">
                                <div class="detalhe-label">📅 Data da Candidatura</div>
                                <div class="detalhe-valor"><?php echo date('d/m/Y H:i', strtotime($candidatura['data_candidatura'])); ?></div>
                            </div>
                        </div>

                        <div class="candidatura-motivo">
                            <h4>Experiência Anterior</h4>
                            <p><?php echo htmlspecialchars($candidatura['experiencia_anterior']); ?></p>
                        </div>

                        <div class="candidatura-motivo">
                            <h4>Motivo da Adoção</h4>
                            <p><?php echo htmlspecialchars($candidatura['motivo_adocao']); ?></p>
                        </div>

                        <?php if ($candidatura['status'] === 'pendente'): ?>
                            <div class="candidatura-acoes">
                                <button onclick="aceitarCandidatura(<?php echo $candidatura['id_candidatura']; ?>)" class="btn-acao-candidatura btn-aceitar">✓ Aceitar</button>
                                <button onclick="rejeitarCandidatura(<?php echo $candidatura['id_candidatura']; ?>)" class="btn-acao-candidatura btn-rejeitar">✗ Rejeitar</button>
                                <button onclick="enviarMensagem(<?php echo $candidatura['id_utilizador']; ?>)" class="btn-acao-candidatura btn-mensagem">💬 Mensagem</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sem-candidaturas">
                <h2>Nenhuma candidatura recebida</h2>
                <p>Quando alguém se candidatar à adoção dos teus animais, verás aqui.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function aceitarCandidatura(id) {
            if (confirm('Aceitar esta candidatura?')) {
                window.location.href = 'responder-candidatura.php?id=' + id + '&status=aceite';
            }
        }

        function rejeitarCandidatura(id) {
            if (confirm('Rejeitar esta candidatura?')) {
                window.location.href = 'responder-candidatura.php?id=' + id + '&status=rejeitada';
            }
        }

        function enviarMensagem(user_id) {
            window.location.href = 'mensagens.php?com=' + user_id;
        }
    </script>
</body>
</html>
