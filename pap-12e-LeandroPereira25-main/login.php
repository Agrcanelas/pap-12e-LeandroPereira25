<?php
require_once 'ligaDB.php';

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Receber e limpar dados do formulário
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validar se os campos não estão vazios
    if (empty($email) || empty($password)) {
        header("Location: formlogin.php?erro=vazio");
        exit();
    }
    
    // Preparar query SQL para prevenir SQL Injection
    $sql = "SELECT id_utilizador, nome, email, password, foto_perfil FROM utilizador WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    // Verificar se o utilizador existe
    if ($resultado->num_rows == 1) {
        $utilizador = $resultado->fetch_assoc();
        
        // Verificar password
        if (password_verify($password, $utilizador['password'])) {
            // Login bem-sucedido!
            $_SESSION['user_id'] = $utilizador['id_utilizador'];
            $_SESSION['user_nome'] = $utilizador['nome'];
            $_SESSION['user_email'] = $utilizador['email'];
            $_SESSION['foto_perfil'] = $utilizador['foto_perfil'] ?? 'uploads/default-avatar.png';
            $_SESSION['logado'] = true;
            
            // Redirecionar para página protegida ou home
            header("Location: dashboard.php");
            exit();
        } else {
            // Password incorreta
            header("Location: formlogin.php?erro=credenciais");
            exit();
        }
    } else {
        // Utilizador não encontrado
        header("Location: formlogin.php?erro=credenciais");
        exit();
    }
    
    $stmt->close();
} else {
    // Se tentar aceder diretamente sem POST, redireciona
    header("Location: formlogin.php");
    exit();
}

$conn->close();
?>