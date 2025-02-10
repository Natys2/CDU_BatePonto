<?php
// backend.php

// Conexão com o banco de dados
$servername = "localhost";
$username = "u757994620_Natyasha"; // padrão do XAMPP
$password = "Na2704@1"; // padrão do XAMPP
$dbname = "u757994620_absoluta"; // nome do banco de dados

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificação de conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receber a ação
$action = $_POST['action'];

switch ($action) {
    case 'fetch':
        // Buscar colaboradores de uma filial
        $filial = $_POST['filial'];
        $sql = "SELECT id, nome, cargo, faltas FROM colaboradores WHERE filial = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $filial);
        $stmt->execute();
        $result = $stmt->get_result();

        $colaboradores = [];
        while ($row = $result->fetch_assoc()) {
            $colaboradores[] = $row;
        }

        echo json_encode(['colaboradores' => $colaboradores]);
        break;

    case 'add':
        // Adicionar novo colaborador
        $nome = $_POST['nome'];
        $cargo = $_POST['cargo'];
        $filial = $_POST['filial'];
        $faltas = 0; // Faltas começam em 0 quando o colaborador é adicionado

        $sql = "INSERT INTO colaboradores (nome, cargo, filial, faltas) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $cargo, $filial, $faltas);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Colaborador adicionado com sucesso']);
        } else {
            echo json_encode(['error' => 'Erro ao adicionar colaborador']);
        }
        break;

    case 'update':
        // Atualizar colaborador
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $cargo = $_POST['cargo'];

        $sql = "UPDATE colaboradores SET nome = ?, cargo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $cargo, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Colaborador atualizado com sucesso']);
        } else {
            echo json_encode(['error' => 'Erro ao atualizar colaborador']);
        }
        break;

    case 'delete':
        // Remover colaborador
        $id = $_POST['id'];

        $sql = "DELETE FROM colaboradores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Colaborador removido com sucesso']);
        } else {
            echo json_encode(['error' => 'Erro ao remover colaborador']);
        }
        break;

    case 'fetchAusencias':
        // Buscar ausências de colaboradores de uma filial
        $filial = $_POST['filial'];
        $sql = "SELECT colaboradores.nome, colaboradores.cargo, DATE_FORMAT(faltas.data_falta, '%d/%m/%Y') AS data_falta, faltas.motivo, faltas.id
                FROM colaboradores
                JOIN faltas ON colaboradores.id = faltas.colaborador_id
                WHERE colaboradores.filial = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $filial);
        $stmt->execute();
        $result = $stmt->get_result();
        $ausencias = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['ausencias' => $ausencias]);
        break;

    case 'removerFalta':
        // Remover falta
        $id = $_POST['id'];

        // Verifica se o ID é válido (não vazio e é um número)
        if (empty($id) || !is_numeric($id)) {
            echo json_encode(['error' => 'ID inválido']);
            break;
        }

        $sql = "DELETE FROM faltas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => 'Falta removida com sucesso']);
        } else {
            echo json_encode(['error' => 'Erro ao remover falta']);
        }
        break;

    default:
        echo json_encode(['error' => 'Ação inválida.']);
}

$conn->close();
?>
