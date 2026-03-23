<?php
require_once 'ligaDB.php';

// Estatísticas melhoradas
$stats = [
    'total_adotados' => 0,
    'total_disponiveis' => 0,
    'especie_popular' => 'Cão'
];

// Total adotados
$sql_adotados = "SELECT COUNT(*) AS total FROM animal WHERE adotado = 1";
$resultado = $conn->query($sql_adotados);
if ($resultado && $linha = $resultado->fetch_assoc()) {
    $stats['total_adotados'] = (int) $linha['total'];
}

// Total disponíveis
$sql_disponiveis = "SELECT COUNT(*) AS total FROM animal WHERE adotado = 0";
$resultado = $conn->query($sql_disponiveis);
if ($resultado && $linha = $resultado->fetch_assoc()) {
    $stats['total_disponiveis'] = (int) $linha['total'];
}

// Espécie mais adotada
$sql_popular = "SELECT especie, COUNT(*) AS total FROM animal WHERE adotado = 1 GROUP BY especie ORDER BY total DESC LIMIT 1";
$resultado = $conn->query($sql_popular);
if ($resultado && $linha = $resultado->fetch_assoc()) {
    $stats['especie_popular'] = htmlspecialchars($linha['especie']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <!-- Barra de Navegação -->
    <?php include 'menu.php'; ?>

    <!-- Cabeçalho -->
    <header class="cabecalho">
        <!-- Carrossel de Imagens -->
        <div class="carrossel">
            <div class="slide ativo" style="background-image: url('imagens/img1.jpg');"></div>
            <div class="slide" style="background-image: url('imagens/img2.jpg');"></div>
            <div class="slide" style="background-image: url('imagens/img3');"></div>
        </div>
        
        <!-- Overlay verde por cima das imagens -->
        <div class="overlay"></div>
        
        <!-- Conteúdo do cabeçalho -->
        <div class="conteudo-cabecalho">
            <h1>SAS</h1>
            <p>Save Animal Souls</p>
            <p class="descricao">
                Neste site acredito que cada vida importa. Trabalho para oferecer apoio, informação e iniciativas que promovem o bem-estar animal. Seja adotando, ajudando ou aprendendo mais sobre como proteger os nossos amigos de todas as patas.
            </p>
            <a href="animais.php"><button>Começar</button></a>
        </div>
        
        <!-- Indicadores (bolinhas) -->
        <div class="indicadores">
            <span class="indicador ativo" data-slide="0"></span>
            <span class="indicador" data-slide="1"></span>
            <span class="indicador" data-slide="2"></span>
        </div>
    </header>
    
    <script>
        // Carrossel automático
        let slideAtual = 0;
        const slides = document.querySelectorAll('.slide');
        const indicadores = document.querySelectorAll('.indicador');
        const totalSlides = slides.length;
        
        function mostrarSlide(index) {
            // Remove classe ativo de todos
            slides.forEach(slide => slide.classList.remove('ativo'));
            indicadores.forEach(ind => ind.classList.remove('ativo'));
            
            // Adiciona classe ativo ao slide atual
            slides[index].classList.add('ativo');
            indicadores[index].classList.add('ativo');
        }
        
        function proximoSlide() {
            slideAtual = (slideAtual + 1) % totalSlides;
            mostrarSlide(slideAtual);
        }
        
        // Muda automaticamente a cada 5 segundos
        setInterval(proximoSlide, 5000);
        
        // Clique nos indicadores
        indicadores.forEach((indicador, index) => {
            indicador.addEventListener('click', () => {
                slideAtual = index;
                mostrarSlide(slideAtual);
            });
        });
    </script>

    <!-- Seção de Conteúdo -->
    <section class="conteudo" id="sobre-nos">
        <div class="texto">
            <h2>Sobre Nós</h2>
            <p>
                A Save Animal Souls é uma organização non-profit dedicada à reabilitação de animais abandonados. 
                Acreditamos que cada vida tem valor e merece uma segunda oportunidade. Através deste site, conectamos animais necessitados com pessoas que podem oferecer um lar amoroso. Com o apoio da comunidade
                apaixonados e do apoio da comunidade, esperemos transformar centenas de vidas - tanto de animais quanto das 
                famílias que os adotam. Junte-se a nós nesta missão!
            </p>
        </div>

        <div class="icone">
            <span>🐾</span>
        </div>
    </section>

    <!-- Estatísticas -->
    <section class="estatisticas">
        <div class="estatistica-item">
            <div class="icone-estatistica">❤️</div>
            <div class="numero"><?php echo $stats['total_adotados'] * 2; ?>+</div>
            <div class="label">Vidas Transformadas</div>
        </div>
        <div class="estatistica-item">
            <div class="icone-estatistica">🎉</div>
            <div class="numero"><?php echo $stats['total_adotados']; ?>+</div>
            <div class="label">Animais Adotados</div>
        </div>
        <div class="estatistica-item">
            <div class="icone-estatistica">🐾</div>
            <div class="numero"><?php echo $stats['total_disponiveis']; ?></div>
            <div class="label">Disponíveis Agora</div>
        </div>
    </section>

    <!-- Como Ajudar -->
    <section class="como-ajudar" id="como-ajudar">
        <h2 class="titulo-secao">Como Podes Ajudar</h2>
        <p class="subtitulo-secao">Existem várias formas de fazer a diferença na vida de um animal</p>
        
        <div class="cards-container">
            <div class="card">
                <div class="card-icone">🎉</div>
                <h3>Animais Adotados</h3>
                <p>Celebramos cada final feliz. Acompanha a histórias de sucesso dos animais já adotados.</p>
                <a href="animais-adotados.php" class="botao-card">Ver Resultados</a>
            </div>

            <div class="card">
                <div class="card-icone">💚</div>
                <h3>Doar</h3>
                <p>Contribuições financeiras ajudam-nos a continuar o trabalho de resgate, tratamento veterinário e cuidados.</p>
                <a href="funcionalidade-indisponivel.php" class="botao-card">Fazer Doação</a>
            </div>

            <div class="card">
                <div class="card-icone">🐾</div>
                <h3>Adotar</h3>
                <p>Encontra o companheiro perfeito. Percorre a nossa listagem de animais disponíveis para adoção.</p>
                <a href="animais.php" class="botao-card">Ver Animais</a>
            </div>

            <div class="card">
                <div class="card-icone">📢</div>
                <h3>Partilhar</h3>
                <p>Ajuda a espalhar a palavra! Partilha as nossas histórias e animais disponíveis para adoção.</p>
                <a href="animais.php" class="botao-card">Partilhar Agora</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="rodape" id="contacto">
        <div class="rodape-conteudo">
            <div class="rodape-coluna">
                <h3>Save Animal Souls</h3>
                <p>Dedicados a salvar e proteger animais desde 2025.</p>
                <div class="redes-sociais">
                    <a href="animais.php" title="Facebook">📘</a>
                    <a href="animais-adotados.php" title="Instagram">📷</a>
                    <a href="meus-animais.php" title="Twitter">🐦</a>
                    <a href="contacto.php" title="Email">✉️</a>
                </div>
            </div>

            <div class="rodape-coluna">
                <h4>Links Rápidos</h4>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="#sobre-nos">Sobre Nós</a></li>
                    <li><a href="animais.php">Adotar</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </div>

            <div class="rodape-coluna">
                <h4>Como Ajudar</h4>
                <ul>
                    <li><a href="animais.php">Adotar um Animal</a></li>
                    <li><a href="funcionalidade-indisponivel.php">Doar Agora</a></li>
                    <li><a href="animais-adotados.php">Ver Histórias de Sucesso</a></li>
                    <li><a href="apadrinhar.php">Apadrinhar</a></li>
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