<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_animal = trim($_POST['nome_animal']);
    $especie = $_POST['especie'];
    $raca = trim($_POST['raca']);
    $idade = trim($_POST['idade']);
    $sexo = $_POST['sexo'];
    $porte = $_POST['porte'];
    $descricao = trim($_POST['descricao']);
    $localidade = trim($_POST['localidade']);
    $id_utilizador = $_SESSION['user_id'];
    
    // Upload da foto
    $foto_animal = 'uploads/animal-default.jpg';
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $arquivo = $_FILES['foto'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($extensao, $extensoes_permitidas) && $arquivo['size'] <= 5000000) {
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            $nome_novo = 'animal_' . time() . '.' . $extensao;
            $caminho = 'uploads/' . $nome_novo;
            if(move_uploaded_file($arquivo['tmp_name'], $caminho)) {
                $foto_animal = $caminho;
            }
        }
    }
    
    $sql = "INSERT INTO animal (id_utilizador, nome_animal, especie, raca, idade, sexo, porte, descricao, foto_animal, localidade) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssss", $id_utilizador, $nome_animal, $especie, $raca, $idade, $sexo, $porte, $descricao, $foto_animal, $localidade);
    
    if($stmt->execute()) {
        header("Location: meus-animais.php?sucesso=adicionado");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Animal - SAS</title>
    <link rel="stylesheet" href="estilo-login.css">
</head>
<body>
    <div class="container-login">
        <div class="caixa-login" style="max-width: 600px;">
            <h2>Adicionar Animal para Adoção</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="grupo-input">
                    <label>Nome do Animal *</label>
                    <input type="text" name="nome_animal" required placeholder="Ex: Max">
                </div>

                <div class="grupo-input">
                    <label>Espécie *</label>
                    <select name="especie" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                        <option value="">Selecione...</option>
                        <option value="Cão">Cão</option>
                        <option value="Gato">Gato</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Raça</label>
                    <input type="text" name="raca" placeholder="Ex: Labrador">
                </div>

                <div class="grupo-input">
                    <label>Idade</label>
                    <input type="text" name="idade" placeholder="Ex: 2 anos">
                </div>

                <div class="grupo-input">
                    <label>Sexo *</label>
                    <select name="sexo" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                        <option value="">Selecione...</option>
                        <option value="Macho">Macho</option>
                        <option value="Fêmea">Fêmea</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Porte</label>
                    <select name="porte" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                        <option value="">Selecione...</option>
                        <option value="Pequeno">Pequeno</option>
                        <option value="Médio">Médio</option>
                        <option value="Grande">Grande</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Localidade</label>
                    <input type="text" name="localidade" placeholder="Ex: Porto">
                </div>

                <div class="grupo-input">
                    <label>Descrição</label>
                    <textarea name="descricao" rows="4" placeholder="Conte mais sobre o animal..." style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <div class="grupo-input">
                    <label>Foto do Animal</label>
                    <input type="file" name="foto" accept="image/*" style="width: 100%; padding: 12px;">
                </div>

                <button type="submit" class="botao-login">Adicionar Animal</button>
            </form>

            <a href="animais.php" class="link-voltar">← Voltar</a>
        </div>
    </div>
</body>
</html>