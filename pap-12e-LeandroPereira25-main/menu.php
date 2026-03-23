<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-logo">
            <a href="index.php">🐾 SAS</a>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Início</a></li>
            <li><a href="animais.php" class="nav-link">Animais</a></li>
            <li><a href="animais-adotados.php" class="nav-link">Adotados</a></li>
            <?php if(isset($_SESSION['logado'])): ?>
                <li><a href="meus-animais.php" class="nav-link">Meus Animais</a></li>
                <li><a href="meus-favoritos.php" class="nav-link">Favoritos</a></li>
                <li><a href="mensagens.php" class="nav-link">Mensagens</a></li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="admin-dashboard.php" class="nav-link">Admin</a></li>
                    <li><a href="admin-denuncias.php" class="nav-link">Denúncias</a></li>
                <?php endif; ?>
   
                <li>
                    <a href="dashboard.php" class="nav-profile">
                        <?php 
                            // Usar foto da sessão ou padrão
                            $foto_perfil = resolve_profile_image($_SESSION['foto_perfil'] ?? null);
                        ?>
                        <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Perfil" class="nav-profile-img">
                    </a>
                </li>
                <li><a href="logout.php" class="nav-link nav-link-logout">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php" class="nav-link nav-link-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>