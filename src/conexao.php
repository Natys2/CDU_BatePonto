<?php
$servername = "localhost";
$username = "u757994620_Natyasha"; // padrão do XAMPP
$password = "Na2704@1"; // padrão do XAMPP
$dbname = "u757994620_absoluta"; // nome do banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die(json_encode(["error" => "Erro de conexão: " . $conn->connect_error]));
}

// Definindo o charset para evitar problemas com caracteres especiais
$conn->set_charset("utf8mb4");

// Função para executar uma consulta preparada
function executeQuery($conn, $sql, $params) {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die(json_encode(["error" => "Erro na preparação da consulta: " . $conn->error]));
    }

    // Se não houver parâmetros, execute diretamente
    if (empty($params)) {
        $stmt->execute();
    } else {
        // Cria um array para armazenar os tipos de dados
        $types = str_repeat("s", count($params)); // 's' para strings
        $stmt->bind_param($types, ...$params); // Usa o operador de expansão
        $stmt->execute();
    }

    return $stmt;
}

// Função para fechar a conexão
function closeConnection($conn) {
    $conn->close();
}
?>