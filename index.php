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
    <div class="barra-navegacao">
        <a class="ativo" href="index.php">Início</a>
        <a href="formlogin.php">Login</a>
        <a href="animais.php">Animais</a>
        <a href="#">Link 3</a>
        <a href="#">Link 4</a>
    </div>
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
            <button>Começar</button>
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
    <section class="conteudo">
        <div class="texto">
            <h2>Sobre Nós</h2>
            <p>
                A Save Animal Souls é uma organização dedicada ao resgate, reabilitação e adoção de animais abandonados. 
                Acredito que cada vida tem valor e merece uma segunda oportunidade. Através do trabalho de voluntários 
                apaixonados e do apoio da comunidade, já transformámos centenas de vidas - tanto de animais quanto das 
                famílias que os adotam. Junte-se a mim nesta missão!
            </p>
        </div>

        <div class="icone">
            <span>🐾</span>
        </div>
    </section>

    <!-- Estatísticas -->
    <section class="estatisticas">
        <div class="estatistica-item">
            <div class="numero">500+</div>
            <div class="label">Animais Salvos</div>
        </div>
        <div class="estatistica-item">
            <div class="numero">80+</div>
            <div class="label">Voluntários Ativos</div>
        </div>
        
    </section>

    <!-- Como Ajudar -->
    <section class="como-ajudar">
        <h2 class="titulo-secao">Como Podes Ajudar</h2>
        <p class="subtitulo-secao">Existem várias formas de fazer a diferença na vida de um animal</p>
        
        <div class="cards-container">
            <div class="card">
                <div class="card-icone">🏠</div>
                <h3>Adotar</h3>
                <p>Dá um lar amoroso a um animal que precisa. A adoção salva vidas e traz alegria para casa.</p>
                <a href="#" class="botao-card">Conhecer Animais</a>
            </div>

            <div class="card">
                <div class="card-icone">💚</div>
                <h3>Doar</h3>
                <p>Contribuições financeiras ajudam-nos a continuar o trabalho de resgate, tratamento veterinário e cuidados.</p>
                <a href="#" class="botao-card">Fazer Doação</a>
            </div>

            <div class="card">
                <div class="card-icone">🤝</div>
                <h3>Voluntariar</h3>
                <p>O teu tempo e dedicação são preciosos. Junta-te à nossa equipa e ajuda diretamente os animais.</p>
                <a href="#" class="botao-card">Ser Voluntário</a>
            </div>

            <div class="card">
                <div class="card-icone">📢</div>
                <h3>Partilhar</h3>
                <p>Ajuda a espalhar a palavra! Partilha as nossas histórias e animais disponíveis para adoção.</p>
                <a href="#" class="botao-card">Partilhar Agora</a>
            </div>
        </div>
    </section>

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
                    <li><a href="#">Início</a></li>
                    <li><a href="#">Sobre Nós</a></li>
                    <li><a href="#">Adotar</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>

            <div class="rodape-coluna">
                <h4>Como Ajudar</h4>
                <ul>
                    <li><a href="#">Adotar um Animal</a></li>
                    <li><a href="#">Fazer Doação</a></li>
                    <li><a href="#">Ser Voluntário</a></li>
                    <li><a href="#">Apadrinhar</a></li>
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