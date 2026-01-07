<?php
require_once 'ligaDB.php';

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Receber e limpar dados do formulário
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];
    
    // Validar se os campos obrigatórios não estão vazios
    if (empty($nome) || empty($email) || empty($password) || empty($confirmar_password)) {
        header("Location: formregisto.php?erro=vazio");
        exit();
    }
    
    // Verificar se as passwords coincidem
    if ($password !== $confirmar_password) {
        header("Location: formregisto.php?erro=passwords");
        exit();
    }
    
    // Verificar se o email já existe
    $sql_verificar = "SELECT id FROM utilizadores WHERE email = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("s", $email);
    $stmt_verificar->execute();
    $resultado = $stmt_verificar->get_result();
    
    if ($resultado->num_rows > 0) {
        // Email já registado
        header("Location: formregisto.php?erro=email_existe");
        exit();
    }
    
    // Encriptar a password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Inserir novo utilizador na base de dados
    $sql = "INSERT INTO utilizadores (nome, email, telefone, password, data_registo) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $telefone, $password_hash);
    
    if ($stmt->execute()) {
        // Registo bem-sucedido
        header("Location: formlogin.php?sucesso=registo");
        exit();
    } else {
        // Erro ao registar
        header("Location: formregisto.php?erro=bd");
        exit();
    }
    
    $stmt->close();
    $stmt_verificar->close();
} else {
    // Se tentar aceder diretamente sem POST, redireciona
    header("Location: formregisto.php");
    exit();
}

$conn->close();
?>