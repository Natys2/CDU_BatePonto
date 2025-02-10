<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Iniciar sessão no início do arquivo
session_start();

$servername = "localhost";
$username = "u757994620_Natyasha"; // padrão do XAMPP
$password = "Na2704@1"; // padrão do XAMPP
$dbname = "u757994620_absoluta"; // nome do banco de dados

// Cria conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = trim($_POST['action']);  // Remove espaços extras
        file_put_contents('php://stderr', "Ação recebida no PHP: " . $action . "\n");  // Log para depuração
    } else {
        echo json_encode(["error" => "Ação não especificada."]);
        exit();
    }

    // Verificar se a ação é 'ausencia'
    if ($action === 'ausencia') {
        file_put_contents('php://stderr', "Ação confirmada: " . $action . "\n");  // Log de confirmação da ação

        // Verifica se os dados necessários estão presentes
        if (isset($_POST['id']) && isset($_POST['data_falta']) && isset($_POST['motivo'])) {
            $colaboradorId = $_POST['id'];
            $dataFalta = $_POST['data_falta'];  // A data da falta fornecida
            $motivo = $_POST['motivo'];  // O motivo da falta fornecido

            file_put_contents('php://stderr', "ID: " . $colaboradorId . " | Data da Falta: " . $dataFalta . " | Motivo: " . $motivo . "\n");  // Log para verificar dados

            // Buscar o nome do colaborador a partir do ID e verificar a filial
            $sqlNome = "SELECT nome, filial FROM colaboradores WHERE id = ?";
            $stmtNome = $conn->prepare($sqlNome);

            if ($stmtNome === false) {
                echo json_encode(['error' => 'Erro ao preparar consulta para buscar nome e filial do colaborador.']);
                exit();
            }

            $stmtNome->bind_param("i", $colaboradorId);
            $stmtNome->execute();
            $stmtNome->store_result();
            $stmtNome->bind_result($nomeColaborador, $filialColaborador);
            $stmtNome->fetch();

            if (!$nomeColaborador) {
                echo json_encode(['error' => 'Colaborador não encontrado.']);
                exit();
            }

            // Verificar se a filial do colaborador corresponde à filial do usuário logado
            if ($_SESSION['filial'] !== $filialColaborador) {
                echo json_encode(['error' => 'Você não tem permissão para registrar faltas para colaboradores de outra filial.']);
                exit();
            }

            // Inserir nova falta na tabela 'faltas' com o nome do colaborador, data e motivo
            $sql = "INSERT INTO faltas (colaborador_id, nome_colaborador, data_falta, motivo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                echo json_encode(['error' => 'Erro ao preparar a consulta para registrar a falta.']);
                exit();
            }

            // A data enviada deve estar no formato YYYY-MM-DD para o MySQL
            $stmt->bind_param("isss", $colaboradorId, $nomeColaborador, $dataFalta, $motivo);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Falta registrada com sucesso']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Erro ao registrar a falta']);
            }
        } else {
            echo json_encode(["error" => "ID, data da falta ou motivo não especificados."]);
        }
    } else {
        echo json_encode(["error" => "Ação inválida. Ação esperada: 'ausencia'."]);
    }

    $stmt->close();
    $stmtNome->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método não permitido. Somente POST é aceito."]);
}
?>
