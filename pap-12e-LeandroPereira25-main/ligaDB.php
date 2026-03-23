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

// Sincronizar role do utilizador na sessão.
// Isto evita erros de permissão quando a role muda na BD e o utilizador já estava autenticado.
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true && isset($_SESSION['user_id'])) {
    $tem_coluna_role = false;
    $check_role = $conn->query("SHOW COLUMNS FROM utilizador LIKE 'role'");
    if ($check_role && $check_role->num_rows > 0) {
        $tem_coluna_role = true;
    }

    if ($tem_coluna_role) {
        $sql_role = "SELECT role FROM utilizador WHERE id_utilizador = ? LIMIT 1";
        $stmt_role = $conn->prepare($sql_role);
        if ($stmt_role) {
            $id_utilizador = (int) $_SESSION['user_id'];
            $stmt_role->bind_param("i", $id_utilizador);
            $stmt_role->execute();
            $resultado_role = $stmt_role->get_result();

            if ($resultado_role && ($linha_role = $resultado_role->fetch_assoc())) {
                $_SESSION['user_role'] = $linha_role['role'] ?? 'user';
            } else {
                $_SESSION['user_role'] = 'user';
            }

            $stmt_role->close();
        }
    } else {
        $_SESSION['user_role'] = 'user';
    }
}
?>