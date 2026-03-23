<?php
require_once 'ligaDB.php';

// Verificar autenticação
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php?redirect=animais.php");
    exit();
}

// Verificar ID do animal
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: animais.php?erro=id_invalido");
    exit();
}

$id_animal = intval($_GET['id']);
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Verificar se user_id está definido
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// Verificar se o animal existe
$check_animal = $conn->prepare("SELECT id_animal FROM animal WHERE id_animal = ?");
$check_animal->bind_param("i", $id_animal);
$check_animal->execute();
$resultado_animal = $check_animal->get_result();

if ($resultado_animal->num_rows == 0) {
    header("Location: animais.php?erro=animal_nao_existe");
    exit();
}

$check_animal->close();

// Verificar se já está nos favoritos
$check_fav = $conn->prepare("SELECT id_favorito FROM favoritos WHERE id_utilizador = ? AND id_animal = ?");
$check_fav->bind_param("ii", $user_id, $id_animal);
$check_fav->execute();
$resultado_fav = $check_fav->get_result();

if ($resultado_fav->num_rows > 0) {
    // Já está nos favoritos - redirecionar
    header("Location: meus-favoritos.php?msg=ja_existe");
    exit();
}

$check_fav->close();

// Adicionar aos favoritos
$sql = "INSERT INTO favoritos (id_utilizador, id_animal, data_criacao) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    header("Location: animais.php?erro=preparar_query");
    exit();
}

$stmt->bind_param("ii", $user_id, $id_animal);

if ($stmt->execute()) {
    header("Location: meus-favoritos.php?sucesso=1");
} else {
    header("Location: animais.php?erro=favorito_erro");
}

$stmt->close();
$conn->close();
?>

