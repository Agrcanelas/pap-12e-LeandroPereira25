<?php
$script_name = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '');
$pagina_atual = basename((string) $script_name);

if (!function_exists('nav_link_class')) {
    function nav_link_class(string $destino, array $aliases = [], string $classes_extra = ''): string
    {
        global $pagina_atual;

        $ativo = ($pagina_atual === $destino) || in_array($pagina_atual, $aliases, true);
        $classes = trim('nav-link' . ($ativo ? ' active' : '') . ($classes_extra !== '' ? ' ' . $classes_extra : ''));

        return $classes;
    }
}
?>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-logo">
            <a href="index.php">🐾 SAS</a>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php" class="<?php echo nav_link_class('index.php'); ?>">Início</a></li>
            <li><a href="animais.php" class="<?php echo nav_link_class('animais.php', ['detalhes-animal.php', 'perfil-utilizador.php', 'denunciar-perfil.php', 'denunciar-utilizador.php']); ?>">Animais</a></li>
            <li><a href="animais-adotados.php" class="<?php echo nav_link_class('animais-adotados.php'); ?>">Adotados</a></li>
            <?php if(isset($_SESSION['logado'])): ?>
                <li><a href="meus-animais.php" class="<?php echo nav_link_class('meus-animais.php', ['adicionar-animal.php', 'editar-animal.php', 'remover-animal.php', 'marcar-adotado.php']); ?>">Meus Animais</a></li>
                <li><a href="meus-favoritos.php" class="<?php echo nav_link_class('meus-favoritos.php', ['adicionar-favorito.php', 'remover-favorito.php']); ?>">Favoritos</a></li>
                <li><a href="mensagens.php" class="<?php echo nav_link_class('mensagens.php', ['enviar-mensagem.php']); ?>">Mensagens</a></li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="admin-dashboard.php" class="<?php echo nav_link_class('admin-dashboard.php', ['admin-utilizador-toggle.php']); ?>">Admin</a></li>
                    <li><a href="admin-denuncias.php" class="<?php echo nav_link_class('admin-denuncias.php', ['admin-denuncia-estado.php', 'admin-denuncia-apagar.php']); ?>">Denúncias</a></li>
                <?php endif; ?>
   
                <li>
                    <a href="dashboard.php" class="nav-profile <?php echo (($pagina_atual === 'dashboard.php' || $pagina_atual === 'editar-perfil.php' || $pagina_atual === 'upload-foto.php') ? 'active' : ''); ?>">
                        <?php 
                            // Usar foto da sessão ou padrão
                            $foto_perfil = resolve_profile_image($_SESSION['foto_perfil'] ?? null);
                        ?>
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Perfil" class="nav-profile-img">
                    </a>
                </li>
                <li><a href="logout.php" class="<?php echo nav_link_class('logout.php', [], 'nav-link-logout'); ?>">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php" class="<?php echo nav_link_class('login.php', ['formlogin.php', 'formregisto.php', 'registo.php'], 'nav-link-login'); ?>">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
    (function () {
        var paginaAtual = (window.location.pathname.split('/').pop() || '').toLowerCase();

        var aliases = {
            'detalhes-animal.php': 'animais.php',
            'perfil-utilizador.php': 'animais.php',
            'denunciar-perfil.php': 'animais.php',
            'denunciar-utilizador.php': 'animais.php',
            'adicionar-animal.php': 'meus-animais.php',
            'editar-animal.php': 'meus-animais.php',
            'remover-animal.php': 'meus-animais.php',
            'marcar-adotado.php': 'meus-animais.php',
            'adicionar-favorito.php': 'meus-favoritos.php',
            'remover-favorito.php': 'meus-favoritos.php',
            'enviar-mensagem.php': 'mensagens.php',
            'admin-utilizador-toggle.php': 'admin-dashboard.php',
            'admin-denuncia-estado.php': 'admin-denuncias.php',
            'admin-denuncia-apagar.php': 'admin-denuncias.php',
            'formlogin.php': 'login.php',
            'formregisto.php': 'login.php',
            'registo.php': 'login.php'
        };

        var paginaMenu = aliases[paginaAtual] || paginaAtual;
        var links = document.querySelectorAll('.nav-menu .nav-link');

        links.forEach(function (link) {
            var href = ((link.getAttribute('href') || '').split('?')[0] || '').toLowerCase();
            if (!href) {
                return;
            }

            if (href === paginaMenu) {
                link.classList.add('active');
            }
        });

        var paginasPerfil = ['dashboard.php', 'editar-perfil.php', 'upload-foto.php'];
        if (paginasPerfil.indexOf(paginaAtual) !== -1) {
            var perfil = document.querySelector('.nav-profile');
            if (perfil) {
                perfil.classList.add('active');
            }
        }
    })();
</script>