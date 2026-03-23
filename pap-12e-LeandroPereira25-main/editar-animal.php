<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: meus-animais.php");
    exit();
}

$id_animal = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Buscar Animal para pré-preencher
$sql = "SELECT * FROM animal WHERE id_animal = ? AND id_utilizador = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_animal, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: meus-animais.php?erro=animal_nao_encontrado");
    exit();
}

$animal = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_animal = trim($_POST['nome_animal']);
    $especie = $_POST['especie'];
    $raca = trim($_POST['raca']);
    $idade = trim($_POST['idade']);
    $sexo = $_POST['sexo'];
    $porte = $_POST['porte'];
    $descricao = trim($_POST['descricao']);
    $localidade = trim($_POST['localidade']);
    $adotado = (isset($_POST['adotado']) && $_POST['adotado'] === '1') ? 1 : 0;
    $foto_animal = $animal['foto_animal'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $arquivo = $_FILES['foto'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extensao, $extensoes_permitidas) && $arquivo['size'] <= 5000000) {
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            // Nome único para evitar cache e colisões de ficheiros.
            $nome_novo = 'animal_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extensao;
            $caminho = 'uploads/' . $nome_novo;

            if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
                // Remove foto antiga local (exceto imagem padrão).
                if (!empty($animal['foto_animal']) &&
                    $animal['foto_animal'] !== 'uploads/animal-default.jpg' &&
                    is_file($animal['foto_animal'])) {
                    @unlink($animal['foto_animal']);
                }
                $foto_animal = $caminho;
            }
        } else {
            $erro = "A imagem deve ser JPG, JPEG, PNG ou GIF e ter no máximo 5MB.";
        }
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE && $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $erro = "Não foi possível carregar a imagem. Tente novamente.";
    }

    if (empty($erro)) {
        $sql_update = "UPDATE animal SET nome_animal = ?, especie = ?, raca = ?, idade = ?, sexo = ?, porte = ?, descricao = ?, localidade = ?, foto_animal = ?, adotado = ? WHERE id_animal = ? AND id_utilizador = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssssiii", $nome_animal, $especie, $raca, $idade, $sexo, $porte, $descricao, $localidade, $foto_animal, $adotado, $id_animal, $user_id);

        if ($stmt_update->execute()) {
            header("Location: meus-animais.php?sucesso=editado");
            exit();
        } else {
            $erro = "Erro ao atualizar: " . $stmt_update->error;
        }

        $stmt_update->close();
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Animal - SAS</title>
    <link rel="stylesheet" href="estilo-login.css">
</head>
<body>
    <div class="container-login">
        <div class="caixa-login" style="max-width: 600px;">
            <h2>Editar Animal</h2>

            <?php if (!empty($erro)): ?>
                <div style="color: #e74c3c; margin-bottom: 15px;"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="grupo-input">
                    <label>Nome do Animal *</label>
                    <input type="text" name="nome_animal" required value="<?php echo htmlspecialchars($animal['nome_animal']); ?>">
                </div>

                <div class="grupo-input">
                    <label>Espécie *</label>
                    <select name="especie" required>
                        <option value="">Selecione...</option>
                        <option value="Cão" <?php echo ($animal['especie'] === 'Cão') ? 'selected' : ''; ?>>Cão</option>
                        <option value="Gato" <?php echo ($animal['especie'] === 'Gato') ? 'selected' : ''; ?>>Gato</option>
                        <option value="Outro" <?php echo ($animal['especie'] === 'Outro') ? 'selected' : ''; ?>>Outro</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Raça</label>
                    <input type="text" name="raca" value="<?php echo htmlspecialchars($animal['raca']); ?>">
                </div>

                <div class="grupo-input">
                    <label>Idade</label>
                    <input type="text" name="idade" value="<?php echo htmlspecialchars($animal['idade']); ?>">
                </div>

                <div class="grupo-input">
                    <label>Sexo *</label>
                    <select name="sexo" required>
                        <option value="">Selecione...</option>
                        <option value="Macho" <?php echo ($animal['sexo'] === 'Macho') ? 'selected' : ''; ?>>Macho</option>
                        <option value="Fêmea" <?php echo ($animal['sexo'] === 'Fêmea') ? 'selected' : ''; ?>>Fêmea</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Porte</label>
                    <select name="porte">
                        <option value="">Selecione...</option>
                        <option value="Pequeno" <?php echo ($animal['porte'] === 'Pequeno') ? 'selected' : ''; ?>>Pequeno</option>
                        <option value="Médio" <?php echo ($animal['porte'] === 'Médio') ? 'selected' : ''; ?>>Médio</option>
                        <option value="Grande" <?php echo ($animal['porte'] === 'Grande') ? 'selected' : ''; ?>>Grande</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Localidade</label>
                    <input type="text" name="localidade" value="<?php echo htmlspecialchars($animal['localidade']); ?>">
                </div>

                <div class="grupo-input">
                    <label>Estado de Adoção</label>
                    <select name="adotado" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                        <option value="0" <?php echo ((int) $animal['adotado'] === 0) ? 'selected' : ''; ?>>Disponível</option>
                        <option value="1" <?php echo ((int) $animal['adotado'] === 1) ? 'selected' : ''; ?>>Já adotado</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Descrição</label>
                    <textarea name="descricao" rows="4"><?php echo htmlspecialchars($animal['descricao']); ?></textarea>
                </div>

                <div class="grupo-input">
                    <label>Foto atual</label>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo htmlspecialchars($animal['foto_animal'] ?: 'uploads/animal-default.jpg'); ?>"
                             alt="Foto atual do animal"
                             style="width: 140px; height: 140px; object-fit: cover; border-radius: 10px; border: 1px solid #ddd;">
                    </div>
                    <label>Mudar foto (opcional)</label>
                    <input type="file" name="foto" accept="image/*">
                </div>

                <button type="submit" class="botao-login">Salvar</button>
            </form>

            <a href="meus-animais.php" class="link-voltar">← Voltar</a>
        </div>
    </div>
</body>
</html>