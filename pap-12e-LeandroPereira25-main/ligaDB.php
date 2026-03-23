<?php
// Configurações da Base de Dados MySQL
define('DB_HOST', 'localhost');     // Endereço do servidor (normalmente localhost)
define('DB_USER', 'root');          // Nome de utilizador MySQL
define('DB_PASS', '');              // Password MySQL (vazio por defeito no XAMPP/WAMP)
define('DB_NAME', 'sas_database');  // Nome da base de dados
define('DEFAULT_PROFILE_IMAGE', 'uploads/default-avatar.png'); // Troca aqui para a imagem default que quiseres
define('DEFAULT_ANIMAL_IMAGE', 'uploads/default-image.jpg'); // Imagem default para animais

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

if (!function_exists('resolve_profile_image')) {
    function resolve_profile_image(?string $foto): string
    {
        $default = DEFAULT_PROFILE_IMAGE;
        $foto = trim((string) $foto);

        if ($foto === '') {
            return $default;
        }

        // Se for URL externa válida, mantém.
        if (filter_var($foto, FILTER_VALIDATE_URL)) {
            return $foto;
        }

        $normalizado = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $foto);
        $caminho_local = __DIR__ . DIRECTORY_SEPARATOR . ltrim($normalizado, DIRECTORY_SEPARATOR);

        return is_file($caminho_local) ? $foto : $default;
    }
}

if (!function_exists('resolve_animal_image')) {
    function resolve_animal_image(?string $foto): string
    {
        $default = DEFAULT_ANIMAL_IMAGE;
        $foto = trim((string) $foto);

        if ($foto === '') {
            return $default;
        }

        // Se for URL externa válida, mantém.
        if (filter_var($foto, FILTER_VALIDATE_URL)) {
            return $foto;
        }

        $normalizado = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $foto);
        $caminho_local = __DIR__ . DIRECTORY_SEPARATOR . ltrim($normalizado, DIRECTORY_SEPARATOR);

        return is_file($caminho_local) ? $foto : $default;
    }
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