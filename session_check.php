<?php
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    // Usuário não está logado, redireciona para a tela de login
    header("Location: default.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Consultar informações do usuário, se necessário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: default.php");
    exit(); // Impede a execução do script se não estiver logado
}
if ($stmt === false) {
    die("Erro na preparação da consulta de verificação de sessão: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Se o usuário não for encontrado, redireciona para a tela de login
    header("Location: default.php");
    exit();
}
?>
