<?php
require_once 'ligaDB.php';

echo "<h2>🔍 Diagnóstico da Base de Dados</h2>";

// 1. Verificar se tabela mensagem existe
echo "<h3>1. Verificar tabela mensagem:</h3>";
$sql_check = "SHOW TABLES LIKE 'mensagem'";
$check_result = $conn->query($sql_check);

if ($check_result && $check_result->num_rows > 0) {
    echo "✅ Tabela EXISTE<br>";
    
    // Ver número de mensagens
    $sql_count = "SELECT COUNT(*) as total FROM mensagem";
    $result = $conn->query($sql_count);
    $row = $result->fetch_assoc();
    echo "📊 Total de mensagens: " . $row['total'] . "<br>";
    
    // Ver estrutura da tabela
    echo "<h3>2. Estrutura da tabela:</h3>";
    $sql_desc = "DESCRIBE mensagem";
    $desc_result = $conn->query($sql_desc);
    echo "<pre>";
    while ($col = $desc_result->fetch_assoc()) {
        echo $col['Field'] . " - " . $col['Type'] . "<br>";
    }
    echo "</pre>";
} else {
    echo "❌ Tabela NÃO existe<br>";
    echo "<p>Criando tabela...</p>";
    
    $sql_create = "CREATE TABLE IF NOT EXISTS mensagem (
        id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
        id_remetente INT NOT NULL,
        id_destinatario INT NOT NULL,
        conteudo TEXT NOT NULL,
        data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_remetente) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
        FOREIGN KEY (id_destinatario) REFERENCES utilizador(id_utilizador) ON DELETE CASCADE,
        INDEX idx_remetente (id_remetente),
        INDEX idx_destinatario (id_destinatario)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql_create) === TRUE) {
        echo "✅ Tabela criada com sucesso!<br>";
    } else {
        echo "❌ Erro: " . $conn->error . "<br>";
    }
}

// 3. Verificar utilizadores
echo "<h3>3. Utilizadores na base de dados:</h3>";
$sql_users = "SELECT id_utilizador, nome, email FROM utilizador LIMIT 5";
$users_result = $conn->query($sql_users);
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Nome</th><th>Email</th></tr>";
while ($user = $users_result->fetch_assoc()) {
    echo "<tr><td>" . $user['id_utilizador'] . "</td><td>" . $user['nome'] . "</td><td>" . $user['email'] . "</td></tr>";
}
echo "</table>";

// 4. Adicionar mensagem de teste
echo "<h3>4. Tentar inserir mensagem de teste:</h3>";
if (isset($_SESSION['user_id'])) {
    $id_user = $_SESSION['user_id'];
    echo "ID do utilizador logado: " . $id_user . "<br>";
    
    // Tentar inserir uma mensagem de teste
    $sql_insert = "INSERT INTO mensagem (id_remetente, id_destinatario, conteudo) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_insert);
    if ($stmt) {
        $dest_id = ($id_user == 1) ? 2 : 1; // Se for user 1, envia para 2, senão para 1
        $msg = "Olá! Esta é uma mensagem de teste!";
        $stmt->bind_param("iis", $id_user, $dest_id, $msg);
        if ($stmt->execute()) {
            echo "✅ Mensagem de teste inserida com sucesso!<br>";
        } else {
            echo "❌ Erro ao inserir: " . $stmt->error . "<br>";
        }
        $stmt->close();
    } else {
        echo "❌ Erro ao preparar statement: " . $conn->error . "<br>";
    }
} else {
    echo "⚠️ Nenhum utilizador logado. <a href='login.php'>Faça login primeiro</a><br>";
}

// 5. Ver todas as mensagens
echo "<h3>5. Todas as mensagens:</h3>";
$sql_all = "SELECT m.*, ur.nome as remetente, ud.nome as destinatario FROM mensagem m 
            JOIN utilizador ur ON m.id_remetente = ur.id_utilizador
            JOIN utilizador ud ON m.id_destinatario = ud.id_utilizador";
$all_result = $conn->query($sql_all);
if ($all_result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>De</th><th>Para</th><th>Mensagem</th><th>Data</th></tr>";
    while ($msg = $all_result->fetch_assoc()) {
        echo "<tr><td>" . $msg['remetente'] . "</td><td>" . $msg['destinatario'] . "</td><td>" . substr($msg['conteudo'], 0, 30) . "...</td><td>" . $msg['data_envio'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "⚠️ Nenhuma mensagem encontrada<br>";
}

$conn->close();
?>
<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2, h3 {
        color: #2D5016;
    }
    table {
        background: white;
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
    }
    pre {
        background: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
</style>
