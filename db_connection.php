<?php
$servername = "localhost";
$username = "u757994620_Natyasha"; // padrão do XAMPP
$password = "Na2704@1"; // padrão do XAMPP
$dbname = "u757994620_absoluta"; // nome do banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Garantir que a conexão use a codificação UTF-8
if (!$conn->set_charset("utf8")) {
    echo "Erro ao definir charset UTF-8: " . $conn->error;
}

// Caso tudo esteja correto, a conexão será bem-sucedida
// echo "Conectado com sucesso ao banco de dados!";
?>
