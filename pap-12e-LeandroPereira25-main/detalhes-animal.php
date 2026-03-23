<?php require_once 'ligaDB.php';

// Buscar avaliações do animal
$id_animal = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql_avaliacoes = "SELECT a.*, u.nome FROM avaliacoes a 
                   JOIN utilizador u ON a.id_utilizador = u.id_utilizador 
                   WHERE a.id_animal = ? 
                   ORDER BY a.data_avaliacao DESC";
$stmt_av = $conn->prepare($sql_avaliacoes);
@$stmt_av->bind_param("i", $id_animal);
@$stmt_av->execute();
$avaliacoes = @$stmt_av->get_result();
@$stmt_av->close();

// Calcular média de classificação
$media_classificacao = 0;
$total_avaliacoes = 0;
$sql_media = "SELECT AVG(classificacao) AS media, COUNT(*) AS total FROM avaliacoes WHERE id_animal = ?";
$stmt_media = $conn->prepare($sql_media);
@$stmt_media->bind_param("i", $id_animal);
@$stmt_media->execute();
$resultado_media = @$stmt_media->get_result();
if ($resultado_media && $linha = $resultado_media->fetch_assoc()) {
    $media_classificacao = round($linha['media'], 1);
    $total_avaliacoes = $linha['total'];
}
@$stmt_media->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Animal - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .detalhes-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 20px;
            animation: fadeInUp 1s ease;
        }

        .detalhes-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            margin-bottom: 40px;
        }

        .foto-detalhes {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .info-animal-detalhes {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .nome-animal {
            font-size: 42px;
            color: #2D5016;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            width: fit-content;
        }

        .status-adotado {
            background: #FF7043;
            color: white;
        }

        .status-disponivel {
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
            color: white;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #66BB6A;
        }

        .info-label {
            color: #999;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-valor {
            color: #2C2C2C;
            font-size: 16px;
            font-weight: 600;
        }

        .descricao-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            margin-bottom: 40px;
        }

        .descricao-section h3 {
            color: #2D5016;
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .descricao-section p {
            color: #555;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .dono-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            margin-bottom: 40px;
        }

        .dono-header {
            font-size: 28px;
            color: #2D5016;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .dono-info {
            display: flex;
            gap: 20px;
            align-items: center;
            margin-bottom: 20px;
        }

        .dono-foto {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #66BB6A;
        }

        .dono-details h4 {
            color: #2C2C2C;
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .dono-details p {
            color: #999;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .dono-contact {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn-contact {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-email {
            background: #2196F3;
            color: white;
        }

        .btn-email:hover {
            background: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        }

        .btn-voltar {
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
            color: white;
        }

        .btn-voltar:hover {
            background: linear-gradient(135deg, #56A156 0%, #66A970 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 187, 106, 0.3);
        }

        .botoes-acao {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .avaliacoes-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            margin-bottom: 40px;
        }

        .avaliacoes-header {
            font-size: 28px;
            color: #2D5016;
            margin-bottom: 25px;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .media-stars {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
        }

        .media-valor {
            font-size: 20px;
            color: #FFB84D;
            font-weight: 700;
        }

        .total-avaliacoes {
            font-size: 14px;
            color: #999;
        }

        .formulario-avaliacao {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #FFB84D;
        }

        .stars-input {
            display: flex;
            gap: 10px;
            margin: 15px 0;
            font-size: 28px;
        }

        .star {
            cursor: pointer;
            color: #ddd;
            transition: all 0.2s ease;
        }

        .star:hover, .star.active {
            color: #FFB84D;
            transform: scale(1.2);
        }

        .avaliacao-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #66BB6A;
        }

        .avaliacao-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .avaliacao-autor {
            font-weight: 600;
            color: #2C2C2C;
        }

        .avaliacao-stars {
            color: #FFB84D;
            font-size: 16px;
        }

        .avaliacao-data {
            font-size: 12px;
            color: #999;
        }

        .avaliacao-texto {
            color: #555;
            line-height: 1.6;
            margin-top: 10px;
        }

        .sem-avaliacoes {
            text-align: center;
            color: #999;
            padding: 30px;
        }

        @media (max-width: 768px) {
            .detalhes-header {
                grid-template-columns: 1fr;
                padding: 20px;
            }

            .nome-animal {
                font-size: 28px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .foto-detalhes {
                height: 300px;
            }

            .botoes-acao {
                flex-direction: column;
            }

            .btn-contact {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'menu.php'; ?>

    <div class="detalhes-container">
        <?php
            // Verificar se o ID foi passado
            if (!isset($_GET['id'])) {
                echo '<div style="text-align: center; padding: 50px; background: white; border-radius: 15px; margin-top: 20px;">';
                echo '<h2 style="color: #e74c3c;">Animal não encontrado!</h2>';
                echo '<p>Volte para a <a href="animais.php" style="color: #66BB6A; text-decoration: none; font-weight: 600;">lista de animais</a></p>';
                echo '</div>';
                exit();
            }

            $id_animal = intval($_GET['id']);

            // Buscar dados do animal
            $sql = "SELECT a.*, u.nome as nome_dono, u.email as email_dono, u.telefone as telefone_dono, u.foto_perfil 
                    FROM animal a 
                    JOIN utilizador u ON a.id_utilizador = u.id_utilizador 
                    WHERE a.id_animal = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_animal);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows == 0) {
                echo '<div style="text-align: center; padding: 50px; background: white; border-radius: 15px; margin-top: 20px;">';
                echo '<h2 style="color: #e74c3c;">Animal não encontrado!</h2>';
                echo '<p>Volte para a <a href="animais.php" style="color: #66BB6A; text-decoration: none; font-weight: 600;">lista de animais</a></p>';
                echo '</div>';
                exit();
            }

            $animal = $resultado->fetch_assoc();
        ?>

        <!-- Header com foto e info básica -->
        <div class="detalhes-header">
            <!-- Foto -->
            <div>
                <img src="<?php echo htmlspecialchars($animal['foto_animal'] ?: 'uploads/animal-default.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($animal['nome_animal']); ?>" 
                     class="foto-detalhes">
            </div>

            <!-- Informações -->
            <div class="info-animal-detalhes">
                <h1 class="nome-animal"><?php echo htmlspecialchars($animal['nome_animal']); ?></h1>
                
                <?php if($animal['adotado']): ?>
                    <span class="status-badge status-adotado">✓ ADOTADO</span>
                <?php else: ?>
                    <span class="status-badge status-disponivel">✓ DISPONÍVEL</span>
                <?php endif; ?>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Espécie</div>
                        <div class="info-valor"><?php echo htmlspecialchars($animal['especie']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Sexo</div>
                        <div class="info-valor"><?php echo htmlspecialchars($animal['sexo']); ?></div>
                    </div>
                    
                    <?php if($animal['idade']): ?>
                    <div class="info-item">
                        <div class="info-label">Idade</div>
                        <div class="info-valor"><?php echo htmlspecialchars($animal['idade']); ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($animal['porte']): ?>
                    <div class="info-item">
                        <div class="info-label">Porte</div>
                        <div class="info-valor"><?php echo htmlspecialchars($animal['porte']); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if($animal['localidade']): ?>
                    <div class="info-item">
                        <div class="info-label">Localidade</div>
                        <div class="info-valor">📍 <?php echo htmlspecialchars($animal['localidade']); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if($animal['raca']): ?>
                    <div class="info-item">
                        <div class="info-label">Raça</div>
                        <div class="info-valor"><?php echo htmlspecialchars($animal['raca']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Descrição completa -->
        <?php if($animal['descricao']): ?>
        <div class="descricao-section">
            <h3>📖 Sobre o <?php echo htmlspecialchars($animal['nome_animal']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($animal['descricao'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- Informações do Dono -->
        <div class="dono-section">
            <div class="dono-header">👤 Contactar o Dono</div>
            
            <div class="dono-info">
                <img src="<?php echo htmlspecialchars($animal['foto_perfil'] ?: 'uploads/default-avatar.png'); ?>" 
                     alt="<?php echo htmlspecialchars($animal['nome_dono']); ?>" 
                     class="dono-foto">
                
                <div class="dono-details">
                    <h4><?php echo htmlspecialchars($animal['nome_dono']); ?></h4>
                    <p>📧 <?php echo htmlspecialchars($animal['email_dono']); ?></p>
                    <?php if($animal['telefone_dono']): ?>
                        <p>📱 <?php echo htmlspecialchars($animal['telefone_dono']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dono-contact">
                <?php if(isset($_SESSION['logado']) && $_SESSION['logado']): ?>
                    <a href="mensagens.php?com=<?php echo $animal['id_utilizador']; ?>" class="btn-contact btn-email">
                        💬 Enviar Mensagem
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-contact btn-email">
                        💬 Login para Mensagem
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seção de Avaliações -->
        <div class="avaliacoes-section">
            <div class="avaliacoes-header">
                <span>⭐ Avaliações</span>
                <div class="media-stars">
                    <span class="media-stars-ico">★</span>
                    <span class="media-valor"><?php echo $media_classificacao; ?></span>
                    <span class="total-avaliacoes">(<?php echo $total_avaliacoes; ?> avaliações)</span>
                </div>
            </div>

            <?php if(isset($_SESSION['logado']) && $_SESSION['logado']): ?>
            <div class="formulario-avaliacao">
                <h4 style="margin-top: 0; color: #2D5016;">Deixar Avaliação</h4>
                <form method="POST" action="salvar-avaliacao.php" style="display: flex; flex-direction: column; gap: 15px;">
                    <input type="hidden" name="id_animal" value="<?php echo $id_animal; ?>">
                    
                    <div>
                        <label style="display: block; margin-bottom: 10px; font-weight: 600; color: #2C2C2C;">Classificação *</label>
                        <div class="stars-input" id="stars-input">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" id="classificacao" name="classificacao" value="0" required>
                    </div>

                    <div>
                        <label for="comentario" style="display: block; margin-bottom: 10px; font-weight: 600; color: #2C2C2C;">Comentário (opcional)</label>
                        <textarea id="comentario" name="comentario" rows="3" placeholder="Partilhe sua opinião..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; resize: vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn-contact btn-email" style="align-self: flex-start;">Enviar Avaliação</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if($avaliacoes && $avaliacoes->num_rows > 0): ?>
                <?php while($avaliacao = $avaliacoes->fetch_assoc()): ?>
                    <div class="avaliacao-item">
                        <div class="avaliacao-header">
                            <span class="avaliacao-autor"><?php echo htmlspecialchars($avaliacao['nome']); ?></span>
                            <span class="avaliacao-stars"><?php echo str_repeat('★', $avaliacao['classificacao']) . str_repeat('☆', 5 - $avaliacao['classificacao']); ?></span>
                        </div>
                        <div class="avaliacao-data"><?php echo date('d/m/Y H:i', strtotime($avaliacao['data_avaliacao'])); ?></div>
                        <?php if($avaliacao['comentario']): ?>
                            <div class="avaliacao-texto"><?php echo htmlspecialchars($avaliacao['comentario']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="sem-avaliacoes">
                    <p>Nenhuma avaliação ainda. Seja o primeiro a avaliar!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Botão Voltar -->
        <div class="botoes-acao">
            <a href="animais.php" class="btn-contact btn-voltar">← Voltar aos Animais</a>
        </div>
    </div>

    <script>
        // Interatividade dos stars de avaliação
        const starsInput = document.getElementById('stars-input');
        const classificacaoInput = document.getElementById('classificacao');

        if (starsInput) {
            const stars = starsInput.querySelectorAll('.star');
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.dataset.value;
                    classificacaoInput.value = value;
                    
                    stars.forEach(s => {
                        if (s.dataset.value <= value) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });

                star.addEventListener('mouseover', function() {
                    const value = this.dataset.value;
                    stars.forEach(s => {
                        if (s.dataset.value <= value) {
                            s.style.color = '#FFB84D';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });

            starsInput.addEventListener('mouseout', function() {
                const active = classificacaoInput.value || 0;
                stars.forEach(s => {
                    if (s.dataset.value <= active) {
                        s.style.color = '#FFB84D';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        }
    </script>

    <!-- Footer -->
    <footer class="rodape">
        <div class="rodape-conteudo">
            <div class="rodape-coluna">
                <h3>Save Animal Souls</h3>
                <p>Dedicados a salvar e proteger animais desde 2025.</p>
                <div class="redes-sociais">
                    <a href="#" title="Facebook">📘</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="Twitter">🐦</a>
                    <a href="#" title="Email">✉️</a>
                </div>
            </div>

            <div class="rodape-coluna">
                <h4>Links Rápidos</h4>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="animais.php">Adotar</a></li>
                    <li><a href="dashboard.php">Minha Conta</a></li>
                </ul>
            </div>

            <div class="rodape-coluna">
                <h4>Contacto</h4>
                <ul>
                    <li>📍 Porto, Portugal</li>
                    <li>📞 +351 913 134 304</li>
                    <li>✉️ a10961@agrcanelas.com</li>
                </ul>
            </div>
        </div>

        <div class="rodape-bottom">
            <p>&copy; 2026 Save Animal Souls. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
