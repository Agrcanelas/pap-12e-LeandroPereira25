<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
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
        // Atualizar BD
        $sql = "UPDATE utilizador SET foto_perfil = ? WHERE id_utilizador = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $caminho, $user_id);
        $stmt->execute();
        
        header("Location: dashboard.php?sucesso=foto");
    } else {
        header("Location: dashboard.php?erro=upload");
    }
}
?>