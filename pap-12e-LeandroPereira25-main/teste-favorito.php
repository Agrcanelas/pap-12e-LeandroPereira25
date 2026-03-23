<?php
// Arquivo de teste para debug do sistema de favoritos
require_once 'ligaDB.php';

echo "<h2>Debug - Teste do Sistema de Favoritos</h2>";

// Verificar sessão
echo "<h3>Status da Sessão:</h3>";
echo "Session status: " . session_status() . "<br>";
echo "Sessão logada: " . (isset($_SESSION['logado']) ? "SIM" : "NÃO") . "<br>";
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NÃO DEFINIDO") . "<br>";

// Verificar tabela favoritos
echo "<h3>Tabela Favoritos:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'favoritos'");
echo "Tabela favoritos existe: " . ($result->num_rows > 0 ? "SIM" : "NÃO") . "<br>";

// Listar colunas
if ($result->num_rows > 0) {
    $columns = $conn->query("SHOW COLUMNS FROM favoritos");
    echo "Colunas da tabela:<br>";
    while ($col = $columns->fetch_assoc()) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
    }
}

// Testar query de favoritos do utilizador 1
echo "<h3>Tester Query de Favoritos (user_id=1):</h3>";
if (isset($_SESSION['user_id'])) {
    $test_id = $_SESSION['user_id'];
} else {
    $test_id = 1; // Teste com user_id 1
}

$sql = "SELECT COUNT(*) as total FROM favoritos WHERE id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
echo "Total de favoritos do user " . $test_id . ": " . $row['total'] . "<br>";

// Testar insert
echo "<h3>Teste Insert:</h3>";
if (isset($_SESSION['logado'])) {
    $test_animal_id = 1;
    $sql = "INSERT INTO favoritos (id_utilizador, id_animal) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE id_utilizador = id_utilizador";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['user_id'], $test_animal_id);
    
    if ($stmt->execute()) {
        echo "✓ Insert executado com sucesso<br>";
    } else {
        echo "✗ Erro no Insert: " . $stmt->error . "<br>";
    }
} else {
    echo "⚠️ Não logado - teste impossível<br>";
}

$conn->close();
?>
