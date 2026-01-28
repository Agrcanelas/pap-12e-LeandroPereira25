<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-logo">
            <a href="index.php">🐾 SAS</a>
        </div>
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-link">Início</a></li>
            <li><a href="animais.php" class="nav-link">Animais</a></li>
            <?php if(isset($_SESSION['logado'])): ?>
                <li><a href="meus-animais.php" class="nav-link">Meus Animais</a></li>
                <li><a href="dashboard.php" class="nav-link">Conta</a></li>
                <li><a href="logout.php" class="nav-link nav-link-logout">Sair</a></li>
            <?php else: ?>
                <li><a href="login.php" class="nav-link nav-link-login">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>