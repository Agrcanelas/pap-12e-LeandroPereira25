<?php
// Configurações da Base de Dados MySQL
define('DB_HOST', 'localhost');     // Endereço do servidor (normalmente localhost)
define('DB_USER', 'root');          // Nome de utilizador MySQL
define('DB_PASS', '');              // Password MySQL (vazio por defeito no XAMPP/WAMP)
define('DB_NAME', 'sas_database');  // Nome da base de dados

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Definir charset para UTF-8 (suporte a caracteres especiais)
$conn->set_charset("utf8mb4");

// Iniciar sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>