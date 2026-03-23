<?php require_once 'ligaDB.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - Save Animal Souls</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .mensagens-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 20px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 25px;
            animation: fadeInUp 1s ease;
            min-height: 600px;
        }

        .lista-conversas {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .conversas-header {
            background: linear-gradient(135deg, #2D5016 0%, #3A6B35 100%);
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 700;
            border-bottom: 3px solid #FFB84D;
        }

        .conversa-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .conversa-item:hover {
            background: #f8f9fa;
            padding-left: 25px;
        }

        .conversa-item.active {
            background: linear-gradient(135deg, rgba(102, 187, 106, 0.15) 0%, rgba(129, 199, 132, 0.15) 100%);
            border-left: 4px solid #66BB6A;
            padding-left: 20px;
        }

        .conversa-foto {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            vertical-align: middle;
            border: 2px solid #ddd;
        }

        .conversa-nome {
            font-weight: 600;
            color: #2C2C2C;
            display: block;
            margin-bottom: 5px;
        }

        .conversa-preview {
            font-size: 12px;
            color: #999;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversa-data {
            font-size: 11px;
            color: #ccc;
            margin-top: 3px;
        }

        .lista-vazia {
            padding: 40px 20px;
            text-align: center;
            color: #999;
        }

        .chat-area {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            background: linear-gradient(135deg, #2D5016 0%, #3A6B35 100%);
            color: white;
            padding: 20px;
            border-bottom: 3px solid #FFB84D;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #FFB84D;
            object-fit: cover;
        }

        .chat-header-info h3 {
            margin: 0;
            font-size: 18px;
        }

        .chat-header-info p {
            margin: 3px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 25px;
            background: #fafafa;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mensagem {
            display: flex;
            gap: 10px;
            animation: slideInLeft 0.4s ease;
        }

        .mensagem.sent {
            justify-content: flex-end;
        }

        .mensagem-foto {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .mensagem.sent .mensagem-foto {
            order: 2;
        }

        .mensagem-conteudo {
            max-width: 60%;
            padding: 12px 16px;
            border-radius: 12px;
            word-wrap: break-word;
        }

        .mensagem.received .mensagem-conteudo {
            background: white;
            color: #2C2C2C;
            border: 1px solid #e0e0e0;
        }

        .mensagem.sent .mensagem-conteudo {
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
            color: white;
        }

        .mensagem-hora {
            font-size: 11px;
            color: #999;
            margin-top: 3px;
        }

        .chat-footer {
            padding: 20px;
            background: white;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 10px;
        }

        .chat-input-group {
            display: flex;
            gap: 10px;
            flex: 1;
            align-items: center;
        }

        .chat-input-group input[type="text"] {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
            font-family: 'Quicksand', sans-serif;
            transition: all 0.3s ease;
        }

        .chat-input-group input[type="text"]:focus {
            outline: none;
            border-color: #66BB6A;
            box-shadow: 0 0 0 3px rgba(102, 187, 106, 0.1);
        }

        .file-upload-input {
            display: none;
        }

        .file-upload-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 1px solid #d9d9d9;
            background: #f7f7f7;
            color: #2D5016;
            font-size: 18px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .file-upload-btn:hover {
            background: #eaf5ea;
            border-color: #66BB6A;
            transform: translateY(-1px);
        }

        .file-upload-btn.has-file {
            background: #66BB6A;
            color: white;
            border-color: #66BB6A;
        }

        .file-upload-name {
            font-size: 12px;
            color: #666;
            max-width: 110px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-enviar-msg {
            padding: 12px 24px;
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Quicksand', sans-serif;
        }

        .btn-enviar-msg:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 187, 106, 0.3);
        }

        .btn-enviar-msg:active {
            transform: translateY(0);
        }

        .chat-vazio {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            color: #999;
        }

        .chat-vazio svg {
            width: 80px;
            height: 80px;
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .btn-nova-msg {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Quicksand', sans-serif;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .btn-nova-msg:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 187, 106, 0.3);
        }

        @media (max-width: 768px) {
            .mensagens-container {
                grid-template-columns: 1fr;
                margin-top: 100px;
            }

            .lista-conversas {
                order: 2;
                max-height: 300px;
            }

            .chat-area {
                order: 1;
            }

            .mensagem-conteudo {
                max-width: 85%;
            }
        }

        .sem-auth {
            max-width: 600px;
            margin: 150px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .sem-auth h2 {
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .sem-auth p {
            color: #666;
            margin-bottom: 20px;
        }

        .sem-auth a {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .sem-auth a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 187, 106, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'menu.php'; ?>

    <?php
        // Verificar se utilizador está logado
        if (!isset($_SESSION['logado']) || !$_SESSION['logado']) {
            echo '<div class="sem-auth">';
            echo '<h2>Faça login para ver mensagens</h2>';
            echo '<p>Precisa estar autenticado para acessar o sistema de mensagens.</p>';
            echo '<a href="login.php">← Ir para login</a>';
            echo '</div>';
            exit();
        }

        $id_utilizador = $_SESSION['user_id'];
        $conversas_unicas = [];
        $id_selecionado = isset($_GET['com']) ? intval($_GET['com']) : null;

        // Compatibilidade com bases de dados que ainda não têm a coluna anexo.
        $coluna_anexo_ok = false;
        $check_anexo = $conn->query("SHOW COLUMNS FROM mensagem LIKE 'anexo'");
        if ($check_anexo && $check_anexo->num_rows > 0) {
            $coluna_anexo_ok = true;
        }

        $campo_anexo = $coluna_anexo_ok ? "m.anexo" : "NULL AS anexo";

        // Buscar todas as conversas do utilizador
        $sql = "SELECT DISTINCT 
                    CASE 
                        WHEN m.id_remetente = ? THEN m.id_destinatario
                        ELSE m.id_remetente
                    END as outro_id,
                    u.nome, u.foto_perfil,
                    m.mensagem, $campo_anexo, m.data_envio
                FROM mensagem m
                JOIN utilizador u ON (
                    (m.id_remetente = ? AND u.id_utilizador = m.id_destinatario) OR
                    (m.id_destinatario = ? AND u.id_utilizador = m.id_remetente)
                )
                WHERE m.id_remetente = ? OR m.id_destinatario = ?
                ORDER BY m.data_envio DESC";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iiiii", $id_utilizador, $id_utilizador, $id_utilizador, $id_utilizador, $id_utilizador);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $ids_vistos = [];
            while ($row = $resultado->fetch_assoc()) {
                if (!in_array($row['outro_id'], $ids_vistos)) {
                    $conversas_unicas[] = $row;
                    $ids_vistos[] = $row['outro_id'];
                }
            }
            $stmt->close();
        }

        if (empty($id_selecionado) && count($conversas_unicas) > 0) {
            $id_selecionado = $conversas_unicas[0]['outro_id'];
        }
    ?>

    <div class="mensagens-container">
        <!-- Lista de Conversas -->
        <div class="lista-conversas">
            <div class="conversas-header">💬 Conversas</div>
            
            <?php if (empty($conversas_unicas)): ?>
                <div class="lista-vazia">
                    <p>Nenhuma conversa ainda</p>
                    <small>Encontre um animal e clique em<br>"Enviar Mensagem"!</small>
                </div>
            <?php else: ?>
                <?php foreach ($conversas_unicas as $conversa): ?>
                    <a href="?com=<?php echo $conversa['outro_id']; ?>" style="text-decoration: none;">
                        <div class="conversa-item <?php echo ($id_selecionado == $conversa['outro_id']) ? 'active' : ''; ?>">
                               <img src="<?php echo htmlspecialchars(resolve_profile_image($conversa['foto_perfil'] ?? null)); ?>" 
                                 alt="<?php echo htmlspecialchars($conversa['nome']); ?>" 
                                 class="conversa-foto">
                            <span class="conversa-nome"><?php echo htmlspecialchars($conversa['nome']); ?></span>
                            <a href="perfil-utilizador.php?id=<?php echo (int) $conversa['outro_id']; ?>" style="font-size: 12px; color: #2D5016; text-decoration: none; font-weight: 600;">Ver perfil</a>
                            <?php
                                $preview_msg = trim((string) ($conversa['mensagem'] ?? ''));
                                if ($preview_msg === '' && !empty($conversa['anexo'])) {
                                    $preview_msg = 'Imagem enviada';
                                }
                            ?>
                            <div class="conversa-preview"><?php echo htmlspecialchars(substr($preview_msg, 0, 40)); ?>...</div>
                            <div class="conversa-data"><?php echo date('d/m H:i', strtotime($conversa['data_envio'])); ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Área de Chat -->
        <div class="chat-area">
            <?php if ($id_selecionado): 
                // Buscar dados do utilizador selecionado
                $sql_user = "SELECT id_utilizador, nome, foto_perfil FROM utilizador WHERE id_utilizador = ?";
                $stmt_user = $conn->prepare($sql_user);
                if ($stmt_user) {
                    $stmt_user->bind_param("i", $id_selecionado);
                    $stmt_user->execute();
                    $user_result = $stmt_user->get_result();
                    $user_data = $user_result->fetch_assoc();
                    $stmt_user->close();
                }

                // Buscar todas as mensagens da conversa
                if ($user_data) {
                          $sql_msgs = "SELECT m.id_mensagem, m.id_remetente, m.id_destinatario, m.mensagem, $campo_anexo, m.data_envio, u.nome, u.foto_perfil 
                                 FROM mensagem m
                                 JOIN utilizador u ON m.id_remetente = u.id_utilizador
                                 WHERE (m.id_remetente = ? AND m.id_destinatario = ?) 
                                    OR (m.id_remetente = ? AND m.id_destinatario = ?)
                                 ORDER BY m.data_envio ASC";
                    $stmt_msgs = $conn->prepare($sql_msgs);
                    if ($stmt_msgs) {
                        $stmt_msgs->bind_param("iiii", $id_utilizador, $id_selecionado, $id_selecionado, $id_utilizador);
                        $stmt_msgs->execute();
                        $msgs_result = $stmt_msgs->get_result();
                    }
                }
            ?>
                <!-- Header do Chat -->
                <div class="chat-header">
                    <a href="perfil-utilizador.php?id=<?php echo (int) $user_data['id_utilizador']; ?>" style="line-height: 0;">
                            <img src="<?php echo htmlspecialchars(resolve_profile_image($user_data['foto_perfil'] ?? null)); ?>" 
                             alt="<?php echo htmlspecialchars($user_data['nome']); ?>">
                    </a>
                    <div class="chat-header-info">
                        <h3><a href="perfil-utilizador.php?id=<?php echo (int) $user_data['id_utilizador']; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($user_data['nome']); ?></a></h3>
                        <p>Online</p>
                    </div>
                </div>

                <!-- Mensagens -->
                <div class="chat-messages" id="chat-messages">
                    <?php if ($msgs_result && $msgs_result->num_rows > 0): ?>
                        <?php while ($msg = $msgs_result->fetch_assoc()): 
                            $é_remetente = ($msg['id_remetente'] == $id_utilizador);
                        ?>
                            <div class="mensagem <?php echo $é_remetente ? 'sent' : 'received'; ?>">
                                <?php if (!$é_remetente): ?>
                                     <img src="<?php echo htmlspecialchars(resolve_profile_image($msg['foto_perfil'] ?? null)); ?>" 
                                         alt="<?php echo htmlspecialchars($msg['nome']); ?>" 
                                         class="mensagem-foto">
                                <?php endif; ?>
                                <div>
                                    <div class="mensagem-conteudo"><?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?></div>

                                    <?php
                                        $anexo = trim((string) ($msg['anexo'] ?? ''));
                                        $anexo_local = __DIR__ . DIRECTORY_SEPARATOR . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $anexo), DIRECTORY_SEPARATOR);
                                        $anexo_disponivel = $anexo !== '' && (filter_var($anexo, FILTER_VALIDATE_URL) || is_file($anexo_local));
                                    ?>
                                    <?php if ($anexo_disponivel): ?>
                                        <div style="margin-top: 8px;">
                                            <a href="<?php echo htmlspecialchars($anexo); ?>" target="_blank" style="display: inline-block; max-width: 240px;">
                                                <img src="<?php echo htmlspecialchars($anexo); ?>" alt="Anexo" style="max-width: 100%; border-radius: 10px; border: 1px solid #ccc;">
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mensagem-hora"><?php echo date('H:i', strtotime($msg['data_envio'])); ?></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #999; padding: 30px;">
                            <p>Nenhuma mensagem ainda...</p>
                            <small>Seja o primeiro a iniciar a conversa!</small>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Input de Mensagem -->
                <div class="chat-footer">
                    <form method="POST" action="enviar-mensagem.php" class="chat-input-group" style="display: flex; gap: 10px; width: 100%;" enctype="multipart/form-data">
                        <input type="hidden" name="para_id" value="<?php echo $id_selecionado; ?>">
                        <input type="text" name="conteudo" placeholder="Escreva a sua mensagem..." style="flex: 1;">
                        <label for="anexo" class="file-upload-btn" title="Anexar imagem">&#128206;</label>
                        <input id="anexo" type="file" name="anexo" accept="image/*" class="file-upload-input">
                        <span id="file-upload-name" class="file-upload-name"></span>
                        <button type="submit" class="btn-enviar-msg">Enviar</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="chat-vazio">
                    <div>
                        <h3>👋 Bem-vindo ao Chat!</h3>
                        <p>Selecione uma conversa na esquerda ou<br>comece uma nova clicando em<br>"Enviar Mensagem" nos detalhes de um animal!</p>
                        <a href="animais.php" style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%); color: white; text-decoration: none; border-radius: 8px;">Ver Animais</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

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

    <script>
        // Auto-scroll para a última mensagem
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Feedback visual ao escolher anexo
        const anexoInput = document.getElementById('anexo');
        const anexoNome = document.getElementById('file-upload-name');
        const anexoBtn = document.querySelector('.file-upload-btn');

        if (anexoInput && anexoNome && anexoBtn) {
            anexoInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    anexoNome.textContent = this.files[0].name;
                    anexoBtn.classList.add('has-file');
                } else {
                    anexoNome.textContent = '';
                    anexoBtn.classList.remove('has-file');
                }
            });
        }
    </script>
</body>
</html>
