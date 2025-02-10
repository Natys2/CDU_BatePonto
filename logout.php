<?php
session_start();

// Verificar se o usuário está logado
if (isset($_SESSION['user_id'])) {
    $servername = "localhost";
    $username = "u757994620_Natyasha";
    $password = "Na2704@1"; 
    $dbname = "u757994620_absoluta"; 
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Garantir que a conexão use a codificação UTF-8
    if (!$conn->set_charset("utf8")) {
        die("Erro ao definir charset UTF-8: " . $conn->error);
    }

    $user_id = $_SESSION['user_id'];
    $session_id = session_id();

    // Atualizar a tabela para desmarcar o usuário como logado
    $sql_update = "UPDATE usuarios SET is_logged_in = 0, session_id = NULL WHERE id = ?";
    if ($stmt_update = $conn->prepare($sql_update)) {
        $stmt_update->bind_param("i", $user_id);
        $stmt_update->execute();
        $stmt_update->close();
    }

    // Destruir a sessão
    session_unset(); // Limpa todas as variáveis da sessão
    session_destroy(); // Destroi a sessão

    header("Location: default.php"); // Redireciona para a página de login
    exit();
} else {
    // Se não estiver logado
    header("Location: default.php");
    exit();
}
?>
