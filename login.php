<?php
session_start(); // Inicia a sessão

$servername = "localhost";
$username = "u757994620_Natyasha"; // padrão do XAMPP
$password = "Na2704@1"; // padrão do XAMPP
$dbname = "u757994620_absoluta"; // nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Garantir que a conexão use a codificação UTF-8
if (!$conn->set_charset("utf8")) {
    die("Erro ao definir charset UTF-8: " . $conn->error);
}

// Verificando se o usuário já está logado

// Verificando se o método de requisição está definido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtendo dados do formulário
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Verificar se os campos estão vazios
    if (empty($email) || empty($senha)) {
        echo "Campos vazios detectados!<br>";
        exit();
    }

    // Preparando a consulta para verificar se o usuário já tem uma sessão ativa
    $sql_check_session = "SELECT * FROM usuarios WHERE email = ? AND is_logged_in = 1";
    if ($stmt_check_session = $conn->prepare($sql_check_session)) {
        $stmt_check_session->bind_param("s", $email);
        $stmt_check_session->execute();
        $result_check_session = $stmt_check_session->get_result();

        if ($result_check_session->num_rows > 0) {
            // Usuário já está logado, não permite novo login
            echo "Este usuário já está logado em outra sessão. Por favor, faça logout primeiro.<a href='default.php'>Voltar!</a>";
    exit();
    exit();
            exit();
        }
        $stmt_check_session->close();
    }

    // Preparando a consulta para verificar as credenciais do usuário
    $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $email, $senha); // Bind email e senha
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Usuário encontrado
            $user = $result->fetch_assoc();

            // Marcar o usuário como logado
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['filial'] = $user['filial']; // Salvando a filial na sessão

            // Atualizar a coluna de login no banco de dados
            $session_id = session_id();

            // Limpar sessão de outros usuários se necessário
            $sql_update = "UPDATE usuarios SET is_logged_in = 0, session_id = NULL WHERE is_logged_in = 1";
            $conn->query($sql_update); // Atualiza todos os outros usuários, removendo o login deles

            // Atualizar o usuário atual com o novo session_id
            $sql_update_current = "UPDATE usuarios SET is_logged_in = 1, session_id = ? WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update_current)) {
                $stmt_update->bind_param("si", $session_id, $user['id']);
                $stmt_update->execute();
                $stmt_update->close();
            }

            // Redirecionar com base na filial do usuário
            if ($user['filial'] === '1000') { // Para RH e Supervisores
                header("Location: cdu-user.php");
                exit(); // Garantir que a execução pare aqui
            } else {
                header("Location: bate-ponto.php");
                exit(); // Garantir que a execução pare aqui
            }
        } else {
            // Usuário não encontrado ou dados incorretos
            echo "Usuário não encontrado ou dados incorretos.<br>";
            exit();
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        // Se não conseguir preparar a consulta
        echo "Erro ao preparar a consulta SQL.<br>";
        exit();
    }
} else {
    echo "Método de requisição inválido.<br>";
    exit();
}


$conn->close();

?>
