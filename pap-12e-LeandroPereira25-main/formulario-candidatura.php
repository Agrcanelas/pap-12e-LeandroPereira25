<?php
require_once 'ligaDB.php';

if (!isset($_SESSION['logado']) || !isset($_GET['id'])) {
    header("Location: animais.php");
    exit();
}

$id_animal = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Buscar dados do animal
$sql_animal = "SELECT * FROM animal WHERE id_animal = ?";
$stmt_animal = $conn->prepare($sql_animal);
$stmt_animal->bind_param("i", $id_animal);
$stmt_animal->execute();
$animal = $stmt_animal->get_result()->fetch_assoc();
$stmt_animal->close();

if (!$animal) {
    header("Location: animais.php?erro=animal_nao_encontrado");
    exit();
}

// Buscar dados do utilizador
$sql_user = "SELECT * FROM utilizador WHERE id_utilizador = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatura para Adoção - SAS</title>
    <link rel="stylesheet" href="estilo-login.css">
</head>
<body>
    <div class="container-login">
        <div class="caixa-login" style="max-width: 700px;">
            <h2>📝 Candidatura para Adoção</h2>
            <p style="color: #666; margin-bottom: 20px;">Candidato: <strong><?php echo htmlspecialchars($animal['nome_animal']); ?></strong></p>

            <form method="POST" action="salvar-candidatura.php">
                <input type="hidden" name="id_animal" value="<?php echo $id_animal; ?>">

                <div class="grupo-input">
                    <label>Nome Completo *</label>
                    <input type="text" name="nome" required value="<?php echo htmlspecialchars($user['nome'] ?? ''); ?>" readonly style="background: #f5f5f5;">
                </div>

                <div class="grupo-input">
                    <label>Email *</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly style="background: #f5f5f5;">
                </div>

                <div class="grupo-input">
                    <label>Telefone *</label>
                    <input type="tel" name="telefone" required value="<?php echo htmlspecialchars($user['telefone'] ?? ''); ?>">
                </div>

                <div class="grupo-input">
                    <label>Situação Habitacional *</label>
                    <select name="situacao_habitacional" required>
                        <option value="">Selecione...</option>
                        <option value="apartamento">Apartamento</option>
                        <option value="casa_pequeno_terreno">Casa com pequeno terreno</option>
                        <option value="casa_grande_terreno">Casa com grande terreno</option>
                        <option value="quinta">Quinta/Terreno amplo</option>
                    </select>
                </div>

                <div class="grupo-input">
                    <label>Tem experiência anterior com animais? *</label>
                    <textarea name="experiencia_anterior" rows="4" required placeholder="Descreva sua experiência com animais..."></textarea>
                </div>

                <div class="grupo-input">
                    <label>Por que deseja adotar este animal? *</label>
                    <textarea name="motivo_adocao" rows="4" required placeholder="Conte-nos o porquê..."></textarea>
                </div>

                <div class="grupo-input">
                    <label>Tem outras pessoas em casa? *</label>
                    <textarea name="composicao_familia" rows="3" placeholder="Ex: Cônjuge, filhos (idades), outros animais..."></textarea>
                </div>

                <div class="grupo-input">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="aceita_visita" value="1" required>
                        <span>Aceito visitas de acompanhamento do animal *</span>
                    </label>
                </div>

                <button type="submit" class="botao-login">Enviar Candidatura</button>
            </form>

            <a href="detalhes-animal.php?id=<?php echo $id_animal; ?>" class="link-voltar">← Voltar</a>
        </div>
    </div>
</body>
</html>
