<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
    $user_id = $_SESSION['user_id'];
    $arquivo = $_FILES['foto'];
    
    // Criar pasta uploads se não existir
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    // Validar arquivo
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array($extensao, $extensoes_permitidas)) {
        header("Location: dashboard.php?erro=formato");
        exit();
    }
    
    if ($arquivo['size'] > 5000000) { // 5MB
        header("Location: dashboard.php?erro=tamanho");
        exit();
    }
    
    // Gerar nome único
    $nome_novo = 'perfil_' . $user_id . '_' . time() . '.' . $extensao;
    $caminho = 'uploads/' . $nome_novo;
    
    if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
        // Criar nova conexão para evitar conflito com triggers
        $conn_update = new mysqli(
            defined('DB_HOST') ? DB_HOST : 'localhost',
            defined('DB_USER') ? DB_USER : 'root',
            defined('DB_PASS') ? DB_PASS : '',
            defined('DB_NAME') ? DB_NAME : 'sas_database'
        );
        
        if ($conn_update->connect_error) {
            // Se falhar, tenta com a conexão original
            $sql = "UPDATE utilizador SET foto_perfil = ? WHERE id_utilizador = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $caminho, $user_id);
            $stmt->execute();
        } else {
            // Usar nova conexão
            $sql = "UPDATE utilizador SET foto_perfil = ? WHERE id_utilizador = ?";
            $stmt_update = $conn_update->prepare($sql);
            $stmt_update->bind_param("si", $caminho, $user_id);
            $stmt_update->execute();
            $stmt_update->close();
            $conn_update->close();
        }
        
        // Guardar na sessão para evitar query posterior
        $_SESSION['foto_perfil'] = $caminho;
        
        header("Location: dashboard.php?sucesso=foto");
    } else {
        header("Location: dashboard.php?erro=upload");
    }
}
?>