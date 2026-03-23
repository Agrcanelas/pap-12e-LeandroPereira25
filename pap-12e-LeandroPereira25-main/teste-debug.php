<?php
// Teste de debug para adicionar-favorito

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "=== TESTE DE DEBUG - ADICIONAR FAVORITO ===<br><br>";

echo "<h2>1. Verificando Sessão</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Logado: " . (isset($_SESSION['logado']) ? "SIM" : "NÃO") . "<br>";
echo "User ID na sessão: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "NÃO DEFINIDO") . "<br>";

echo "<h2>2. Verificando GET</h2>";
echo "ID do animal: " . (isset($_GET['id']) ? $_GET['id'] : "NÃO DEFINIDO") . "<br>";

echo "<h2>3. Simulando conexão</h2>";
require_once 'ligaDB.php';

if ($conn->connect_error) {
    echo "Erro de conexão: " . $conn->connect_error . "<br>";
} else {
    echo "✓ Conexão bem-sucedida<br>";
    
    echo "<h2>4. Verificando tabela favoritos</h2>";
    $test_query = "SHOW TABLES LIKE 'favoritos'";
    $result = $conn->query($test_query);
    
    if ($result && $result->num_rows > 0) {
        echo "✓ Tabela favoritos existe<br>";
        
        // Testar com simulação
        if (isset($_SESSION['logado']) && isset($_GET['id'])) {
            $user_id = $_SESSION['user_id'];
            $animal_id = intval($_GET['id']);
            
            echo "<h2>5. Preparando Insert</h2>";
            echo "User ID: $user_id<br>";
            echo "Animal ID: $animal_id<br>";
            
            $sql = "INSERT INTO favoritos (id_utilizador, id_animal, data_criacao) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $animal_id);
                
                if ($stmt->execute()) {
                    echo "✓ Insert executado com sucesso!<br>";
                    echo "Redirecionando para meus-favoritos.php...<br>";
                    // header("Location: meus-favoritos.php?sucesso=1");
                } else {
                    echo "✗ Erro no execute: " . $stmt->error . "<br>";
                }
                
                $stmt->close();
            } else {
                echo "✗ Erro ao preparar statement: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "✗ Tabela favoritos NÃO existe<br>";
    }
    
    $conn->close();
}

echo "<hr>";
echo "<a href='teste-favorito.php'>Voltar aos testes iniciais</a>";
?>
